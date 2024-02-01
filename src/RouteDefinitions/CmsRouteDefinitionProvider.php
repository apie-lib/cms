<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\CmsApiDropdownOption\RouteDefinitions\DropdownOptionsForExistingObjectRouteDefinition;
use Apie\CmsApiDropdownOption\RouteDefinitions\DropdownOptionsForNewObjectRouteDefinition;
use Apie\Common\ActionDefinitionProvider;
use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Common\ActionDefinitions\ModifyResourceActionDefinition;
use Apie\Common\ActionDefinitions\ReplaceResourceActionDefinition;
use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\RouteDefinitions\ActionHashmap;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Psr\Log\LoggerInterface;

class CmsRouteDefinitionProvider implements RouteDefinitionProviderInterface
{
    private const CLASSES = [
        CreateResourceFormRouteDefinition::class,
        CreateResourceCommitRouteDefinition::class,
        DisplayResourceRouteDefinition::class,
        DisplayResourceOverviewRouteDefinition::class,
        ModifyResourceFormRouteDefinition::class,
        RemoveResourceFormRouteDefinition::class,
        RemoveResourceFormCommitRouteDefinition::class,
        RunGlobalMethodCommitRouteDefinition::class,
        RunGlobalMethodFormRouteDefinition::class,
        RunMethodCallOnSingleResourceCommitRouteDefinition::class,
        RunMethodCallOnSingleResourceFormRouteDefinition::class,
    ];

    public function __construct(
        private ActionDefinitionProvider $actionDefinitionProvider,
        private LoggerInterface $logger,
    ) {
    }

    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        $routes = [];
        $definition = new DashboardRouteDefinition($boundedContext->getId());
        $routes[$definition->getOperationId()] = $definition;
        $definition = new LastActionResultRouteDefinition($boundedContext->getId());
        $routes[$definition->getOperationId()] = $definition;
        foreach ($this->actionDefinitionProvider->provideActionDefinitions($boundedContext, $apieContext) as $actionDefinition) {
            $found = false;
            foreach (self::CLASSES as $routeDefinitionClass) {
                $routeDefinition = $routeDefinitionClass::createFrom($actionDefinition);
                if ($routeDefinition) {
                    $routes[$routeDefinition->getOperationId()] = $routeDefinition;
                    $found = true;
                }
            }
            if (class_exists(DropdownOptionsForNewObjectRouteDefinition::class) &&
                ($actionDefinition instanceof CreateResourceActionDefinition || $actionDefinition instanceof ReplaceResourceActionDefinition)) {
                $routeDefinition = new DropdownOptionsForNewObjectRouteDefinition(
                    $actionDefinition->getResourceName(),
                    $actionDefinition->getBoundedContextId(),
                );
                $routes[$routeDefinition->getOperationId()] = $routeDefinition;
            }
            if (class_exists(DropdownOptionsForExistingObjectRouteDefinition::class) && $actionDefinition instanceof ModifyResourceActionDefinition) {
                $routeDefinition = new DropdownOptionsForExistingObjectRouteDefinition(
                    $actionDefinition->getResourceName(),
                    $actionDefinition->getBoundedContextId(),
                );
                $routes[$routeDefinition->getOperationId()] = $routeDefinition;
            }
            if (class_exists(DropdownOptionsForGlobalMethodRouteDefinition::class) && $actionDefinition instanceof RunGlobalMethodDefinition) {
                $routeDefinition = new DropdownOptionsForGlobalMethodRouteDefinition(
                    $actionDefinition->getResourceName(),
                    $actionDefinition->getBoundedContextId(),
                    $actionDefinition->getMethod(),
                );
                $routes[$routeDefinition->getOperationId()] = $routeDefinition;
            }
            if (!$found) {
                $this->logger->debug('No route definition created for ' . get_debug_type($actionDefinition));
            }
        }

        return new ActionHashmap($routes);
    }
}
