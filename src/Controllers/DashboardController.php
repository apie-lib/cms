<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\Services\ResponseFactory;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stringable;

class DashboardController
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilder,
        private readonly ResponseFactory $responseFactory,
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
        return $this->responseFactory->createComponentPageRender($component, $context);
    }
}
