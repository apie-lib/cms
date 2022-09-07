<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\GetResourceListController;
use Apie\Common\Actions\GetListAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class DisplayResourceOverviewRouteDefinition extends AbstractCmsRouteDefinition
{
    public function __construct(ReflectionClass $class, BoundedContextId $id)
    {
        parent::__construct($class, $id);
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/resource/' . $this->class->getShortName());
    }
    /**
     * @return class-string<object>
     */
    public function getController(): string
    {
        return GetResourceListController::class;
    }

    public function getAction(): string
    {
        return GetListAction::class;
    }

    public function getOperationId(): string
    {
        return 'cms.resource.' . $this->class->getShortName();
    }
}
