<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\RouteDefinitions\ActionHashmap;
use Apie\Core\RouteDefinitions\RouteDefinitionProviderInterface;

class CmsRouteDefinitionProvider implements RouteDefinitionProviderInterface
{
    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        $actions = [];
        $definition = new DashboardRouteDefinition($boundedContext->getId());
        $actions[$definition->getOperationId()] = $definition;
        return new ActionHashmap($actions);
    }
}
