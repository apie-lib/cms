<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\GetResourceController;
use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\GetResourceActionDefinition;
use Apie\Common\Actions\GetItemAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;

class DisplayResourceRouteDefinition extends AbstractCmsRouteDefinition
{
    public static function createFrom(ActionDefinitionInterface $actionDefinition): ?AbstractCmsRouteDefinition
    {
        if ($actionDefinition instanceof GetResourceActionDefinition) {
            return new self($actionDefinition->getResourceName(), $actionDefinition->getBoundedContextId());
        }
        return null;
    }

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
        return new UrlRouteDefinition('/resource/' . $this->class->getShortName() . '/{id}');
    }
    /**
     * @return class-string<object>
     */
    public function getController(): string
    {
        return GetResourceController::class;
    }

    public function getAction(): string
    {
        return GetItemAction::class;
    }

    public function getOperationId(): string
    {
        return 'cms.resource.detail.' . $this->class->getShortName();
    }
}
