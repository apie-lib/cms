<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\FormCommitController;
use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\StreamGetterActionDefinition;
use Apie\Common\Actions\RunItemMethodAction;
use Apie\Common\Actions\StreamItemMethodAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;
use ReflectionMethod;

/**
 * Route definition for running method on single resource to stream a resource
 */
class StreamMethodCallOnSingleResourceRouteDefinition extends AbstractCmsRouteDefinition
{
    /**
     * @param ReflectionClass<EntityInterface> $className
     */
    public function __construct(ReflectionClass $className, ReflectionMethod $method, BoundedContextId $boundedContextId)
    {
        parent::__construct($className, $boundedContextId, $method);
    }

    public function getOperationId(): string
    {
        return 'form-stream-single-' . $this->class->getShortName() . '-run-' . $this->method->name;
    }
    
    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        $methodName = RunItemMethodAction::getDisplayNameForMethod($this->method);
        if ($methodName === '__invoke') {
            return new UrlRouteDefinition('/resource/action/' . $this->class->getShortName() . '/{id}');
        }
        return new UrlRouteDefinition('/resource/action/' . $this->class->getShortName() . '/{id}/' . $methodName);
    }

    public function getController(): string
    {
        return FormCommitController::class;
    }

    public function getAction(): string
    {
        return StreamItemMethodAction::class;
    }

    public static function createFrom(ActionDefinitionInterface $actionDefinition): ?AbstractCmsRouteDefinition
    {
        if ($actionDefinition instanceof StreamGetterActionDefinition) {
            return new self($actionDefinition->getResourceName(), $actionDefinition->getMethod(), $actionDefinition->getBoundedContextId());
        }
        return null;
    }
}
