<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\RemoveResourceFormController;
use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RemoveResourceActionDefinition;
use Apie\Common\Actions\RemoveObjectAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class RemoveResourceFormRouteDefinition extends AbstractCmsRouteDefinition
{
    public static function createFrom(ActionDefinitionInterface $actionDefinition): ?AbstractCmsRouteDefinition
    {
        if ($actionDefinition instanceof RemoveResourceActionDefinition) {
            return new self($actionDefinition->getResourceName(), $actionDefinition->getBoundedContextId());
        }
        return null;
    }

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
        return new UrlRouteDefinition('/resource/delete/' . $this->class->getShortName() . '/{id}');
    }

    public function getController(): string
    {
        return RemoveResourceFormController::class;
    }

    public function getAction(): string
    {
        return RemoveObjectAction::class;
    }

    public function getOperationId(): string
    {
        return 'delete-resource-form-' . $this->class->getShortName();
    }
}
