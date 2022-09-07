<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\CreateResourceFormController;
use Apie\Common\Actions\CreateObjectAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class CreateResourceFormRouteDefinition extends AbstractCmsRouteDefinition
{
    public function __construct(ReflectionClass $class, BoundedContextId $boundedContextId)
    {
        parent::__construct($class, $boundedContextId);
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/resource/create/' . $this->class->getShortName());
    }

    public function getController(): string
    {
        return CreateResourceFormController::class;
    }

    public function getAction(): string
    {
        return CreateObjectAction::class;
    }

    public function getOperationId(): string
    {
        return 'create-resource-form-' . $this->class->getShortName();
    }
}
