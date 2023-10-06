<?php
namespace Apie\Cms\RouteDefinitions;

use Apie\Cms\Controllers\FormCommitController;
use Apie\Common\Actions\RunItemMethodAction;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use ReflectionClass;
use ReflectionMethod;

class RunMethodCallOnSingleResourceCommitRouteDefinition extends AbstractCmsRouteDefinition
{
    public function __construct(ReflectionClass $class, ReflectionMethod $method, BoundedContextId $boundedContextId)
    {
        parent::__construct($class, $boundedContextId, $method);
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function getUrl(): UrlRouteDefinition
    {
        $methodName = $this->method->getName();
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
        return RunItemMethodAction::class;
    }

    public function getOperationId(): string
    {
        $methodName = $this->method->getName();
        $suffix = $methodName === '__invoke' ? '' : ('-' . $methodName);
        return 'call-resource-commit-' . $this->class->getShortName() . $suffix;
    }
}
