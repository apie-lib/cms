<?php
namespace Apie\Cms\MenuStructure;

use Apie\Cms\IconResolver;
use Apie\Cms\RouteDefinitions\AbstractCmsRouteDefinition;
use Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider;
use Apie\Common\MenuStructure\MenuBuilder;
use Apie\Common\MenuStructure\MenuNode;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\Translator\ValueObjects\MenuHeader;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;

class MainMenuBuilder
{
    public function __construct(
        private readonly CmsRouteDefinitionProvider $routeDefinitionProvider,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly ApplicationConfiguration $applicationConfiguration,
        private readonly IconResolver $iconResolver
    ) {
    }

    public function buildMenu(
        ApieContext $apieContext,
        ?BoundedContextId $prio = null
    ): MenuNode {
        $menu = new MenuNode(
            name: MenuHeader::createRoot($apieContext),
            route: null,
            icon: null,
        );
        $children = $menu->children;
        $prioBoundedContext = $this->boundedContextHashmap[$prio?->toNative()] ?? null;
        if ($prioBoundedContext) {
            $subcontext = $apieContext->withContext(ContextConstants::BOUNDED_CONTEXT_ID, $prio?->toNative())
                ->registerInstance($prioBoundedContext);
            $children[$prio?->toNative()] = $this->buildMenuForBoundedContext($prioBoundedContext, $subcontext);
        }
        foreach ($this->boundedContextHashmap as $key => $boundedContext) {
            if ($key === $prio?->toNative()) {
                continue;
            }
            $subcontext = $apieContext->withContext(ContextConstants::BOUNDED_CONTEXT_ID, $key)
                ->registerInstance($boundedContext);
            $children[$key] = $this->buildMenuForBoundedContext($boundedContext, $subcontext);
        }
        $menu->children = $children;
        return $menu->prune();
    }

    public function buildMenuForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): MenuNode
    {
        $prefix = $boundedContext->getId()->toNative() . '.';
        $menuBuilder = new MenuBuilder($prefix);
        $apieContext = $apieContext->withContext(
            ContextConstants::MAIN_MENU_BUILDER,
            1
        )->registerInstance($this);
        $routeDefinitions = $this->routeDefinitionProvider->getActionsForBoundedContext(
            $boundedContext,
            $apieContext,
        );
        $configuration = $this->applicationConfiguration->createConfiguration(
            $apieContext,
            $this->boundedContextHashmap,
            $boundedContext->getId()
        );

        /** @var AbstractCmsRouteDefinition $routeDefinition */
        foreach ($routeDefinitions as $routeDefinition) {
            $url = $routeDefinition->getMainMenuUri();
            if (!$url || !in_array($routeDefinition->getMethod(), [RequestMethod::ANY, RequestMethod::GET])) {
                continue;
            }
            $action = $routeDefinition->getAction();
            $subcontext = $apieContext->withMultipleContext($routeDefinition->getRouteAttributes());
            $allowed = $action::isAuthorized($subcontext, true);
            $resourceName = $subcontext->getContext(ContextConstants::RESOURCE_NAME, false);
            $icon = null;
            if ($resourceName && class_exists($resourceName)) {
                $icon = $this->iconResolver->resolve($resourceName);
            }
            foreach ($this->createUrlList($boundedContext, $url) as $createdUrl) {
                $createdList = $createdUrl->toStringList();
                // $apieContext is deliberate choice as we want to be able to change translations per bounded context
                $menuBuilder->addLeaf($createdList, new MenuNode(
                    name: MenuHeader::createRoot($apieContext, $boundedContext->getId() . '.' . $createdList->join('.')),
                    route: $configuration->getContextUrl($routeDefinition->getUrl()->toNative()),
                    icon: $icon,
                    allowed: $allowed
                ));
            }
        }
        $root = $menuBuilder->getRoot();
        $root->allowed = array_any($root->children->toArray(), function (MenuNode $node) {
            return $node->allowed;
        });
        $root->icon = $this->iconResolver->resolve($boundedContext->getId());
        return $menuBuilder->getRoot();
    }

    /**
     * @return UrlRouteDefinition[]
     */
    private function createUrlList(BoundedContext $boundedContext, UrlRouteDefinition $url): array
    {
        $placeholders = $url->getPlaceholders();
        $urlList = [];
        $replacements = [
            'extension' => 'csv',
        ];
        foreach ($placeholders as $placeholder) {
            if ($placeholder === ContextConstants::RESOURCE_NAME) {
                foreach ($boundedContext->resources as $resource) {
                    $urlList[$boundedContext->getId() . '.' . $resource->getShortName()] = $url->withFilledInPlaceholders([
                        ContextConstants::RESOURCE_NAME => $resource->getShortName(),
                        ...$replacements,
                    ]);
                }
            }
        }
        return $urlList ? : ['' => $url];
    }
}
