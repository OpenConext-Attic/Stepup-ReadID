<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Controller;

use Psr\Log\LoggerInterface;
use StepupReadId\Application\PendingSession\GetPendingSessionQuery;
use StepupReadId\Application\ReadySession\GetStoredReadySessionQuery;
use StepupReadId\Domain\PendingSession\Exception\PendingSessionNotFoundException;
use Surfnet\GsspBundle\Service\AuthenticationService;
use Surfnet\GsspBundle\Service\StateHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/verify", methods={"POST"}, name="verify-ready-session", format="json")
 */
class VerifyReadySessionController extends AbstractController
{
    use HandleTrait;

    /** @var LoggerInterface */
    private $logger;
    /** @var AuthenticationService */
    private $authenticationService;
    /** @var StateHandlerInterface */
    private $stateHandler;

    public function __construct(
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        StateHandlerInterface $stateHandler
    ) {
        $this->authenticationService = $authenticationService;
        $this->logger                = $logger;
        $this->messageBus            = $messageBus;
        $this->stateHandler          = $stateHandler;
    }

    public function __invoke(): Response
    {
        if (!$this->authenticationService->authenticationRequired()) {
            $this->logger->error('There is no pending authentication request from SP');

            return $this->errorResponse('noauth', 'There is no pending authentication request from SP');
        }

        $readySession = $this->handle(new GetStoredReadySessionQuery());

        try {
            $pendingSession = $this->handle(new GetPendingSessionQuery($readySession->id()->value()));
        } catch (HandlerFailedException $e) {
            $exception = $e->getPrevious();

            if ($exception instanceof PendingSessionNotFoundException) {
                return $this->errorResponse('notfound', 'Not found');
            }

            $this->logger->error('Bad verification request: ' . $exception->getMessage());

            return $this->errorResponse('badrequest', $exception->getMessage());
        }

        if (!$pendingSession->isConfirmed()) {
            return $this->successResponse(false);
        }

        $this->stateHandler->authenticate();

        $this->logger->info('Session found, user verified');

        return $this->successResponse(true);
    }

    private function errorResponse(string $code, string $message): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error' => ['code' => $code, 'message' => $message],
        ]);
    }

    private function successResponse(bool $confirmed): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'payload' => ['confirmed' => $confirmed],
        ]);
    }
}
