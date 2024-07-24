<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ApieFacade;
use Apie\Core\Actions\ActionResponse;
use Apie\Core\Actions\ActionResponseStatus;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\UuidV4;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\Serializer\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FormCommitController
{
    public function __construct(
        protected readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ApieFacade $apieFacade,
        private readonly ApplicationConfiguration $applicationConfiguration,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);
        $action = $this->apieFacade->createAction($context);
        $data = ($action)($context, $context->getContext(ContextConstants::RAW_CONTENTS));
        return $this->createResponse($request, $data);
    }

    /**
     * Creates redirect response and stores a few things in the session. It redirects to the most sensible layout.
     * If the action gave an error:
     * - store validation errors and the filled in values in the session
     * - redirect to the same uri again.
     * If the action succeeded:
     * - remove values in the session
     * - redirect to if applicable:
     *   - resource/<resource>/<id>
     *   - resource/<resource>
     *   - last-action-result/<result-id>
     */
    protected function createResponse(ServerRequestInterface $request, ActionResponse $output): ResponseInterface
    {
        if ($output->result instanceof ResponseInterface) {
            return $output->result;
        }
        $configuration = $this->applicationConfiguration->createConfiguration(
            $output->apieContext,
            $this->boundedContextHashmap,
            new BoundedContextId($output->apieContext->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );

        $redirectUrl = (string) $request->getUri();
        $session = $output->apieContext->getContext(SessionInterface::class);
        assert($session instanceof SessionInterface);
        if ($output->getStatusCode() < 300) {
            $session->remove('_filled_in');
            $session->remove('_validation_errors');
            if ($output->apieContext->hasContext(ContextConstants::RESOURCE_NAME)) {
                $class = new ReflectionClass($output->apieContext->getContext(ContextConstants::RESOURCE_NAME));
                $redirectUrl = $configuration->getContextUrl('resource/' . $class->getShortName());
                if ($output->apieContext->hasContext(ContextConstants::RESOURCE_ID) && $output->status !== ActionResponseStatus::DELETED) {
                    $redirectUrl = $configuration->getContextUrl(
                        'resource/' . $class->getShortName() . '/' . $output->apieContext->getContext(ContextConstants::RESOURCE_ID)
                    );
                }
            }
            if (isset($output->result) && $output->status !== ActionResponseStatus::DELETED) {
                if ($output->result instanceof EntityInterface) {
                    $baseClass = $output->result->getId()::getReferenceFor();
                    $redirectUrl = $configuration->getContextUrl(
                        'resource/' . $baseClass->getShortName() . '/' . $output->result->getId()->toNative()
                    );
                } else {
                    $previousResults = $session->get('_output_results', []);
                    $uniqueId = UuidV4::createRandom()->toNative();
                    $previousResults[$uniqueId] = $output->result;
                    $session->set('_output_results', $previousResults);
                    $redirectUrl = $configuration->getContextUrl('last-action-result/' . $uniqueId);
                }
            }
        }

        if (isset($output->error) && $output->apieContext->hasContext(SessionInterface::class)) {
            $contents = $output->apieContext->getContext(ContextConstants::RAW_CONTENTS);
            unset($contents['_csrf']);
            $session->set('_filled_in', $contents);
        
            if (
                $output->error instanceof ValidationException
            ) {
                $session->set('_validation_errors', $output->error->getErrors()->toArray());
            } else {
                $session->set('_validation_errors', ['' => $output->error->getMessage()]);
            }
        }

        return $this->responseFactory->createRedirect($redirectUrl, $output->apieContext);
    }
}
