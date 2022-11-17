<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\CreateResourceFormController;
use Apie\Cms\Controllers\ModifyResourceFormController;
use Apie\Common\Actions\CreateObjectAction;
use Apie\Common\Actions\ModifyObjectAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class ModifyResourceFormRouteDefinition extends AbstractCmsRouteDefinition
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
        return new UrlRouteDefinition('/resource/edit/' . $this->class->getShortName() . '/{id}');
    }

    public function getController(): string
    {
        return ModifyResourceFormController::class;
    }

    public function getAction(): string
    {
        return ModifyObjectAction::class;
    }

    public function getOperationId(): string
    {
        return 'modify-resource-form-' . $this->class->getShortName();
    }
}
