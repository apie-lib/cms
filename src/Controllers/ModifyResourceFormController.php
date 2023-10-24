<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\LayoutPicker;
use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class ModifyResourceFormController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ComponentRendererInterface $renderer,
        private readonly LayoutPicker $layoutPicker
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);

        $action = $this->apieFacade->createAction($context);
        $class = $action::getInputType(
            new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME))
        );
        $layout = $this->layoutPicker->pickLayout($request);
        $component = $this->componentFactory->createFormForResourceCreation(
            'Modify ' . $class->getShortName(),
            $class,
            new BoundedContextId($request->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)),
            $context,
            $layout
        );
        $html = $this->renderer->render($component);
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($html))
            ->withHeader('Content-Type', 'text/html');
    }
}
