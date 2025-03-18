<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\LayoutPicker;
use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Core\IdentifierUtils;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;

class RunMethodCallOnSingleResourceFormController
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
            $context->getContext(ContextConstants::METHOD_CLASS),
            $context->getContext(ContextConstants::METHOD_NAME)
        );
        $method = $action::getInputType(
            new ReflectionClass($request->getAttribute(ContextConstants::METHOD_CLASS)),
            $method
        );
        assert($method instanceof ReflectionMethod);
        $id = $context->getContext(ContextConstants::RESOURCE_ID);
        $resource = $this->apieFacade->find(
            IdentifierUtils::entityClassToIdentifier(new ReflectionClass($context->getContext(ContextConstants::RESOURCE_NAME)))
                ->newInstance($id),
            new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );
        $context = $context->withContext(ContextConstants::RESOURCE, $resource);
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
