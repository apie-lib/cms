<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\RouteDefinitions\ActionHashmap;
use Apie\Core\RouteDefinitions\RouteDefinitionProviderInterface;

class CmsRouteDefinitionProvider implements RouteDefinitionProviderInterface
{
    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        $actions = [];
        $definition = new DashboardRouteDefinition($boundedContext->getId());
        $actions[$definition->getOperationId()] = $definition;

        $getAllContext = $apieContext->withContext(RequestMethod::class, RequestMethod::GET)
            /*->withContext(RestApiRouteDefinition::OPENAPI_ALL, true)*/
            ->registerInstance($boundedContext);
        foreach ($boundedContext->resources->filterOnApieContext($getAllContext) as $resource) {
            $definition = new DisplayResourceOverviewRouteDefinition($resource, $boundedContext->getId());
            $actions[$definition->getOperationId()] = $definition;
        }

        return new ActionHashmap($actions);
    }
}
