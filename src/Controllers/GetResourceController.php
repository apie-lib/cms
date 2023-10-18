<?php
namespace Apie\Cms\Controllers;

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

class GetResourceController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ComponentRendererInterface $renderer
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);

        $action = $this->apieFacade->createAction($context);
        $data = ($action)($context, $context->getContext(ContextConstants::RAW_CONTENTS));
        $component = $this->componentFactory->createResource(
            $data,
            new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME)),
            new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );
        $html = $this->renderer->render($component);
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($html))
            ->withHeader('Content-Type', 'text/html');
    }
}
