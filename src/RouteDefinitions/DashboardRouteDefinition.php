<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\DashboardController;
use Apie\Common\ContextConstants;
use Apie\Core\Actions\HasRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;

class DashboardRouteDefinition implements HasRouteDefinition
{
    public function __construct(private readonly BoundedContextId $id)
    {
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/');
    }
    /**
     * @return class-string<object>
     */
    public function getController(): string
    {
        return DashboardController::class;
    }
    /**
     * @return array<string, mixed>
     */
    public function getRouteAttributes(): array
    {
        return [
            ContextConstants::BOUNDED_CONTEXT_ID => $this->id->toNative(),
        ];
    }
    public function getOperationId(): string
    {
        return 'apie.cms.dashboard.' . $this->id;
    }
}
