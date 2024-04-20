<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\LayoutPicker;
use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;

class RunGlobalMethodFormController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly LayoutPicker $layoutPicker,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);
        $context->checkAuthorization();
        $action = $this->apieFacade->createAction($context);
        $method = new ReflectionMethod(
            $context->getContext(ContextConstants::SERVICE_CLASS),
            $context->getContext(ContextConstants::METHOD_NAME)
        );
        $method = $action::getInputType(
            new ReflectionClass($request->getAttribute(ContextConstants::SERVICE_CLASS)),
            $method
        );
        assert($method instanceof ReflectionMethod);
        $layout = $this->layoutPicker->pickLayout($request);
        $component = $this->componentFactory->createFormForMethod(
            $request->getAttribute(ContextConstants::METHOD_NAME) ? : 'Form',
            $method,
            new BoundedContextId($request->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)),
            $context,
            $layout
        );
        return $this->responseFactory->createComponentPageRender($component, $context);
    }
}
