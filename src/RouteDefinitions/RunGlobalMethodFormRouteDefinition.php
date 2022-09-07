<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\RunGlobalMethodFormController;
use Apie\Common\Actions\RunAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionMethod;

class RunGlobalMethodFormRouteDefinition extends AbstractCmsRouteDefinition
{
    public function __construct(ReflectionMethod $method, BoundedContextId $boundedContextId)
    {
        parent::__construct($method->getDeclaringClass(), $boundedContextId, $method);
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        $methodName = $this->method->getName();
        if ($methodName === '__invoke') {
            return new UrlRouteDefinition('action/' . $this->method->getDeclaringClass()->getShortName());
        }
        return new UrlRouteDefinition('action/' . $this->method->getDeclaringClass()->getShortName() . '/' . $methodName);
    }

    public function getController(): string
    {
        return RunGlobalMethodFormController::class;
    }

    public function getAction(): string
    {
        return RunAction::class;
    }

    public function getOperationId(): string
    {
        $methodName = $this->method->getName();
        $suffix = $methodName === '__invoke' ? '' : ('-' . $methodName);
        return 'form-call-method-' . $this->method->getDeclaringClass()->getShortName() . $suffix;
    }
}
