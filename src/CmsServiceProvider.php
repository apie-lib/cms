<?php
namespace Apie\Cms;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: cms.yaml
 * @codecoverageIgnore
 */
class CmsServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->app->singleton(
            \Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider::class,
            function ($app) {
                return new \Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider(
                    $app->make(\Apie\Common\ActionDefinitionProvider::class),
                    $app->make(\Psr\Log\LoggerInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider::class,
            array(
              0 =>
              array(
                'name' => 'apie.common.route_definition',
              ),
            )
        );
        $this->app->tag([\Apie\Cms\RouteDefinitions\CmsRouteDefinitionProvider::class], 'apie.common.route_definition');
        $this->app->singleton(
            \Apie\Cms\Controllers\DashboardController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\DashboardController(
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class),
                    $app->make('apie.cms.dashboard_content')
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\DashboardController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\DashboardController::class], 'controller.service_arguments');
        $this->app->bind('apie.cms.dashboard_content', \Apie\Cms\EmptyDashboard::class);
        
        $this->app->singleton(
            \Apie\Cms\EmptyDashboard::class,
            function ($app) {
                return new \Apie\Cms\EmptyDashboard(
                
                );
            }
        );
        $this->app->singleton(
            \Apie\Cms\Controllers\GetResourceListController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\GetResourceListController(
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\GetResourceListController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\GetResourceListController::class], 'controller.service_arguments');
        $this->app->singleton(
            \Apie\Cms\Controllers\RunGlobalMethodFormController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\RunGlobalMethodFormController(
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\RunGlobalMethodFormController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\RunGlobalMethodFormController::class], 'controller.service_arguments');
        $this->app->singleton(
            \Apie\Cms\Controllers\RunMethodCallOnSingleResourceFormController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\RunMethodCallOnSingleResourceFormController(
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\RunMethodCallOnSingleResourceFormController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\RunMethodCallOnSingleResourceFormController::class], 'controller.service_arguments');
        $this->app->singleton(
            \Apie\Cms\Controllers\CreateResourceFormController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\CreateResourceFormController(
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\CreateResourceFormController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\CreateResourceFormController::class], 'controller.service_arguments');
        $this->app->singleton(
            \Apie\Cms\Controllers\ModifyResourceFormController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\ModifyResourceFormController(
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\ModifyResourceFormController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\ModifyResourceFormController::class], 'controller.service_arguments');
        $this->app->singleton(
            \Apie\Cms\Controllers\FormCommitController::class,
            function ($app) {
                return new \Apie\Cms\Controllers\FormCommitController(
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\Common\ApieFacade::class),
                    $app->make(\Apie\HtmlBuilders\Configuration\ApplicationConfiguration::class),
                    $app->make(\Apie\Core\BoundedContext\BoundedContextHashmap::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Cms\Controllers\FormCommitController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Cms\Controllers\FormCommitController::class], 'controller.service_arguments');
        $this->app->singleton(
            'cms.layout.graphite_design_system',
            function ($app) {
                return \Apie\CmsLayoutGraphite\GraphiteDesignSystemLayout::createRenderer(
                
                );
                
            }
        );
        
    }
}
