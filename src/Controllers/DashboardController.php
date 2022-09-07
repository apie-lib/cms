<?php
namespace Apie\Cms\Controllers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stringable;

class DashboardController
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilder,
        private readonly ComponentRendererInterface $renderer,
        private readonly string|Stringable $dashboardContents = ''
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilder->createGeneralContext([]);
        $boundedContextId = new BoundedContextId($request->getAttribute('boundedContextId'));
        $component = $this->componentFactory->createWrapLayout(
            'Dashboard',
            $boundedContextId,
            $context,
            $this->componentFactory->createRawContents($this->dashboardContents)
        );
        $html = $this->renderer->render($component);
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($html))
            ->withHeader('Content-Type', 'text/html');
    }
}
