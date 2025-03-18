<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ApieFacade;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class GetResourceListController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ResponseFactory $responseFactory
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);

        $action = $this->apieFacade->createAction($context);
        $data = ($action)($context, $context->getContext(ContextConstants::RAW_CONTENTS));
        $component = $this->componentFactory->createResourceOverview(
            $data,
            new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME)),
            new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );

        return $this->responseFactory->createComponentPageRender($component, $data->apieContext);
    }
}
