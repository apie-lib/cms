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

class RemoveResourceFormController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly LayoutPicker $layoutPicker
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);
        $context->checkAuthorization();
        $action = $this->apieFacade->createAction($context);
        $class = $action::getInputType(
            new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME))
        );
        $component = $this->componentFactory->createFormForResourceRemoval(
            'Remove ' . $class->getShortName(),
            $class,
            new BoundedContextId($request->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)),
            $context,
            $this->layoutPicker->pickLayout($request)
        );
        return $this->responseFactory->createComponentPageRender($component, $context);
    }
}
