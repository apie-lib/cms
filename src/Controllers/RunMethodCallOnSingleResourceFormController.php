<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\LayoutPicker;
use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\IdentifierUtils;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
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
        private readonly ComponentRendererInterface $renderer,
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
        $html = $this->renderer->render($component, $context);
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($html))
            ->withHeader('Content-Type', 'text/html');
    }
}
