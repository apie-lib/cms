<?php
namespace Apie\Cms\Controllers;

use Apie\Common\ContextConstants;
use Apie\Core\Actions\ActionResponse;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LastActionResultController
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilder,
        private readonly ComponentRendererInterface $renderer
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilder->createGeneralContext([]);
        $boundedContextId = new BoundedContextId($request->getAttribute('boundedContextId'));
        $id = $request->getAttribute('id');
        $session = $context->getContext(SessionInterface::class);
        $actionResults = $session->get('_output_results', []);
        $psr17Factory = new Psr17Factory();
        if (($actionResults[$id] ?? null) instanceof ActionResponse) {
            $component = $this->componentFactory->createWrapLayout(
                'Action result',
                $boundedContextId,
                $context,
                $this->componentFactory->createResource(
                    $actionResults[$id],
                    new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME)),
                    new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
                )
            );
            $html = $this->renderer->render($component);
            
            return $psr17Factory->createResponse(200)
                ->withBody($psr17Factory->createStream($html))
                ->withHeader('Content-Type', 'text/html');
        }
        $redirectUrl = (string) $request->getUri() . '/../../';
        return $psr17Factory->createResponse(301)
            ->withHeader('location', $redirectUrl);
    }
}
