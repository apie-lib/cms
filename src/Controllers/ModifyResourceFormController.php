<?php
namespace Apie\Cms\Controllers;

use Apie\Cms\LayoutPicker;
use Apie\Cms\Services\ResponseFactory;
use Apie\Common\ApieFacade;
use Apie\Common\IntegrationTestLogger;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\IdentifierUtils;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\Serializer\Serializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class ModifyResourceFormController
{
    public function __construct(
        private readonly ApieFacade $apieFacade,
        private readonly ComponentFactory $componentFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly LayoutPicker $layoutPicker
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::CMS => true]);
        $context->checkAuthorization();
        $action = $this->apieFacade->createAction($context);
        $class = $action::getInputType(
            new ReflectionClass($request->getAttribute(ContextConstants::RESOURCE_NAME))
        );
        
        // TODO: copied from ModifyObjectAction, make it shared
        $resourceClass = new ReflectionClass($context->getContext(ContextConstants::RESOURCE_NAME));
        $id = $context->getContext(ContextConstants::RESOURCE_ID);
        if (!$resourceClass->implementsInterface(EntityInterface::class)) {
            throw new InvalidTypeException($resourceClass->name, 'EntityInterface');
        }
        try {
            $resource = $this->apieFacade->find(
                IdentifierUtils::entityClassToIdentifier($resourceClass)->newInstance($id),
                new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
            );
        } catch (InvalidStringForValueObjectException|EntityNotFoundException $error) {
            IntegrationTestLogger::logException($error);
        }
        if (isset($resource)) {
            $context = $context->withContext(ContextConstants::RESOURCE, $resource);
            $class = new ReflectionClass($resource);
            if (empty($context->getContext(ContextConstants::RAW_CONTENTS, false))) {
                /** @var Serializer $serializer */
                $serializer = $context->getContext(Serializer::class);
                $context = $context->withContext(
                    ContextConstants::RAW_CONTENTS,
                    json_decode(json_encode($serializer->normalize(
                        $resource,
                        $context
                    )), true)
                );
            }
        }
        $layout = $this->layoutPicker->pickLayout($request);
        $component = $this->componentFactory->createFormForResourceModification(
            'Modify ' . $class->getShortName(),
            $class,
            new BoundedContextId($request->getAttribute(ContextConstants::BOUNDED_CONTEXT_ID)),
            $context,
            $layout
        );
        return $this->responseFactory->createComponentPageRender($component, $context);
    }
}
