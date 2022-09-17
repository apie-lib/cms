<?php
namespace Apie\Cms\Controllers;

use Apie\Common\ApieFacade;
use Apie\Common\ContextConstants;
use Apie\Core\Actions\ActionResponse;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Serializer\EncoderHashmap;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class FormCommitController
{
    public function __construct(
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ApieFacade $apieFacade
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

        $redirectUrl = $request->getUri();
        if ($output->getStatusCode() < 300) {
            $class = new ReflectionClass($output->apieContext->getContext(ContextConstants::RESOURCE_NAME));
            $redirectUrl = '/cms/resource/' . $class->getShortName();
        }

        return $psr17Factory->createResponse(301)->withHeader('Location', $redirectUrl);
    }
}
