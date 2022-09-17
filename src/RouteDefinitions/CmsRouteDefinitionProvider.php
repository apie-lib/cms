<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\RouteDefinitions\ActionHashmap;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;

class CmsRouteDefinitionProvider implements RouteDefinitionProviderInterface
{
    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        $actions = [];
        $definition = new DashboardRouteDefinition($boundedContext->getId());
        $actions[$definition->getOperationId()] = $definition;

        $postContext = $apieContext->withContext(RequestMethod::class, RequestMethod::POST)
            ->withContext(ContextConstants::CREATE_OBJECT, true)
            ->registerInstance($boundedContext);
        foreach ($boundedContext->resources->filterOnApieContext($postContext) as $resource) {
            $definition = new CreateResourceFormRouteDefinition($resource, $boundedContext->getId());
            $actions[$definition->getOperationId()] = $definition;
            $definition = new CreateResourceCommitRouteDefinition($resource, $boundedContext->getId());
            $actions[$definition->getOperationId()] = $definition;
        }

        $getAllContext = $apieContext->withContext(RequestMethod::class, RequestMethod::GET)
            ->registerInstance($boundedContext);
        foreach ($boundedContext->resources->filterOnApieContext($getAllContext) as $resource) {
            $definition = new DisplayResourceOverviewRouteDefinition($resource, $boundedContext->getId());
            $actions[$definition->getOperationId()] = $definition;
        }

        $globalActionContext = $apieContext->withContext(ContextConstants::GLOBAL_METHOD, true);
        foreach ($boundedContext->actions->filterOnApieContext($globalActionContext) as $action) {
            $definition = new RunGlobalMethodFormRouteDefinition($action, $boundedContext->getId());
            $actions[$definition->getOperationId()] = $definition;
        }

        return new ActionHashmap($actions);
    }
}
