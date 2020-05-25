<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\SAML;

use Psr\Log\LoggerInterface;
use SAML2\Assertion;
use SAML2\Response;
use StepupReadId\Application\PendingSession\GetPendingSessionQuery;
use StepupReadId\Application\ReadySession\GetStoredReadySessionQuery;
use StepupReadId\Application\Session\GetSessionQuery;
use Surfnet\GsspBundle\Saml\ResponseContextInterface;
use Surfnet\GsspBundle\Service\ResponseServiceInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use function sprintf;

final class SamlResponseFactory
{
    use HandleTrait;

    /** @var ResponseServiceInterface */
    private $responseService;
    /** @var ResponseContextInterface */
    private $responseContext;
    /** @var LoggerInterface */
    private $logger;
    /** @var array<string,mixed> */
    private $readIdAttributeMappings;

    /**
     * @param array<string,mixed> $readIdAttributeMappings
     */
    public function __construct(
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        ResponseContextInterface $responseContext,
        ResponseServiceInterface $responseService,
        array $readIdAttributeMappings
    ) {
        $this->logger                  = $logger;
        $this->responseContext         = $responseContext;
        $this->responseService         = $responseService;
        $this->messageBus              = $messageBus;
        $this->readIdAttributeMappings = $readIdAttributeMappings;
    }

    public function create(): Response
    {
        return $this->createResponse();
    }

    public function fromValidatedSession(): Response
    {
        $response = $this->createResponse();

        try {
            $readySession   = $this->handle(new GetStoredReadySessionQuery());
            $pendingSession = $this->handle(new GetPendingSessionQuery($readySession->id()->value()));
            $session        = $this->handle(new GetSessionQuery($pendingSession->sessionId()->value()));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        $attributes = [];
        foreach ($this->readIdAttributeMappings as $readIdAttribute => $samlAttribute) {
            if (!$session->document()->has($readIdAttribute)) {
                continue;
            }

            $attributes[$samlAttribute] = [$session->document()->get($readIdAttribute)->value()];
        }

        $assertion = $response->getAssertions()[0];
        if ($assertion instanceof Assertion) {
            $assertion->setAttributes($attributes);
        }

        return $response;
    }

    private function createResponse(): Response
    {
        $this->logger->info('Create sso response');

        $response = $this->responseService->createResponse();

        $this->logger->notice(sprintf(
            'Saml response created with id "%s", request ID: "%s"',
            $response->getId(),
            $this->responseContext->getRequestId()
        ));

        return $response;
    }
}
