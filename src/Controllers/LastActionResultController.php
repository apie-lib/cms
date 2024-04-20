<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Factories\FieldDisplayComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LastActionResultController
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilder,
        private readonly ResponseFactory $responseFactory,
        private readonly FieldDisplayComponentFactory $fieldDisplayComponentFactory
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilder->createFromRequest($request, [ContextConstants::CMS => true]);
        $boundedContextId = new BoundedContextId($request->getAttribute('boundedContextId'));
        $id = $request->getAttribute('id');
        $session = $context->getContext(SessionInterface::class);
        $actionResults = $session->get('_output_results', []);
        if (array_key_exists($id, $actionResults)) {
            $component = $this->componentFactory->createWrapLayout(
                'Action result',
                $boundedContextId,
                $context,
                $this->fieldDisplayComponentFactory->createDisplayFor($actionResults[$id], $context)
            );
            return $this->responseFactory->createComponentPageRender($component, $context);
        }
        $redirectUrl = (string) $request->getUri() . '/../../';
        return $this->responseFactory->createRedirect($redirectUrl);
    }
}
