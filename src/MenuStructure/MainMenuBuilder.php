<?php
namespace Apie\Cms\MenuStructure;

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
use Apie\Core\ValueObjects\UrlRouteDefinition;

class MainMenuBuilder
{
    public function __construct(
        private CmsRouteDefinitionProvider $routeDefinitionProvider,
        private BoundedContextHashmap $boundedContextHashmap
    ) {
    }

    public function buildMenu(ApieContext $apieContext, ?BoundedContextId $prio = null): MenuNode
    {
        $menu = new MenuNode(
            id: 'root',
            name: '',
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
        return $menu;
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
        
        /** @var AbstractCmsRouteDefinition $routeDefinition */
        foreach ($routeDefinitions as $routeDefinition) {
            $url = $routeDefinition->getMainMenuUri();
            if (!$url || !in_array($routeDefinition->getMethod(), [RequestMethod::ANY, RequestMethod::GET])) {
                continue;
            }
            foreach ($this->createUrlList($boundedContext, $url) as $suffix => $createdUrl) {
                $createdList = $createdUrl->toStringList();
                $menuBuilder->addLeaf($createdList, new MenuNode(
                    id: $prefix . $routeDefinition->getOperationId() . $suffix,
                    name: $createdList->last(),
                    route: $createdUrl->toNative(),
                    icon: null,
                ));
            }
        }
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
                    $urlList['.' . $resource->getShortName()] = $url->withFilledInPlaceholders([
                        ContextConstants::RESOURCE_NAME => $resource->getShortName(),
                        ...$replacements,
                    ]);
                }
            }
        }
        return $urlList ? : ['' => $url];
    }
}
