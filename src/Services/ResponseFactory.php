<?php
namespace Apie\Cms\Services;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactory
{
    private readonly Psr17Factory $psr17Factory;

    public function __construct(
        private readonly ComponentRendererInterface $renderer
    ) {
        $this->psr17Factory = new Psr17Factory();
    }

    public function createRedirect(string $redirectUrl): ResponseInterface
    {
        return $this->psr17Factory->createResponse(301)->withHeader('Location', $redirectUrl);
    }

    public function createComponentPageRender(ComponentInterface $component, ApieContext $context): ResponseInterface
    {
        $html = $this->renderer->render($component, $context);
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($html))
            ->withHeader('Content-Type', 'text/html');
    }
}
