<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\HasActionDefinition;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextId;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractCmsRouteDefinition implements HasRouteDefinition, HasActionDefinition
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function __construct(
        protected readonly ReflectionClass $class,
        protected readonly ?BoundedContextId $boundedContextId = null,
        protected readonly ?ReflectionMethod $method = null
    ) {
    }

    final public function getRouteAttributes(): array
    {
        $actionClass = $this->getAction();
        $attributes = $actionClass::getRouteAttributes($this->class, $this->method);
        $attributes[ContextConstants::APIE_ACTION] = $this->getAction();
        $attributes[ContextConstants::OPERATION_ID] = $this->getOperationId();
        $attributes[ContextConstants::BOUNDED_CONTEXT_ID] = $this->boundedContextId->toNative();
        return $attributes;
    }
}
