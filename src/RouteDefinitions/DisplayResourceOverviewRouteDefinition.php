<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\GetResourceListController;
use Apie\Common\Actions\GetListAction;
use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\HasActionDefinition;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class DisplayResourceOverviewRouteDefinition implements HasRouteDefinition, HasActionDefinition
{
    public function __construct(private readonly ReflectionClass $className, private readonly BoundedContextId $id)
    {
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/resource/' . $this->className->getShortName());
    }
    /**
     * @return class-string<object>
     */
    public function getController(): string
    {
        return GetResourceListController::class;
    }
    /**
     * @return array<string, mixed>
     */
    public function getRouteAttributes(): array
    {
        return [
            /*RestApiRouteDefinition::OPENAPI_ALL => true,*/
            ContextConstants::RESOURCE_NAME => $this->className->name,
            ContextConstants::BOUNDED_CONTEXT_ID => $this->id->toNative(),
            ContextConstants::OPERATION_ID => $this->getOperationId(),
            ContextConstants::APIE_ACTION => $this->getAction(),
        ];
    }

    public function getAction(): string
    {
        return GetListAction::class;
    }

    public function getOperationId(): string
    {
        return 'cms.resource.' . $this->className->getShortName();
    }
}
