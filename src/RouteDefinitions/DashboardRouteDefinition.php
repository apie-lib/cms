<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\DashboardController;
use Apie\Common\Actions\GetListAction;
use Apie\Common\Enums\UrlPrefix;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextConstants;
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
        return 'cms.dashboard';
    }

    public function getAction(): string
    {
        return GetListAction::class;
    }

    public function getUrlPrefixes(): UrlPrefixList
    {
        return new UrlPrefixList([UrlPrefix::CMS]);
    }
}
