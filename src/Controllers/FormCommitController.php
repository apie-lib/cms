<?php
namespace Apie\Cms\Controllers;

use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Core\Actions\ActionResponse;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\Serializer\EncoderHashmap;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class FormCommitController
{
    public function __construct(
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ApieFacade $apieFacade,
        private readonly ApplicationConfiguration $applicationConfiguration,
        private readonly BoundedContextHashmap $boundedContextHashmap
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);

        $action = $this->apieFacade->createAction($context);
        $data = ($action)($context, $context->getContext(ContextConstants::RAW_CONTENTS));

        return $this->createResponse($request, $data);
    }

    private function createResponse(ServerRequestInterface $request, ActionResponse $output): ResponseInterface
    {
        $psr17Factory = new Psr17Factory();
        $configuration = $this->applicationConfiguration->createConfiguration(
            $output->apieContext,
            $this->boundedContextHashmap,
            new BoundedContextId($output->apieContext->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );

        $redirectUrl = (string) $request->getUri();
        if ($output->getStatusCode() < 300 && $output->apieContext->hasContext(ContextConstants::RESOURCE_NAME)) {
            $class = new ReflectionClass($output->apieContext->getContext(ContextConstants::RESOURCE_NAME));
            $redirectUrl = $configuration->getContextUrl('resource/' . $class->getShortName());
        }

        return $psr17Factory->createResponse(301)->withHeader('Location', $redirectUrl);
    }
}
