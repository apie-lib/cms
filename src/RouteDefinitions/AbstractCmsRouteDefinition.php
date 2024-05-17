<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Common\Enums\UrlPrefix;
use Apie\Common\Interfaces\HasActionDefinition;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextConstants;
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
        $attributes[ContextConstants::CMS] = true;
        $attributes[ContextConstants::OPERATION_ID] = $this->getOperationId();
        $attributes[ContextConstants::BOUNDED_CONTEXT_ID] = $this->boundedContextId->toNative();
        return $attributes;
    }

    final public function getUrlPrefixes(): UrlPrefixList
    {
        return new UrlPrefixList([UrlPrefix::CMS]);
    }
}
