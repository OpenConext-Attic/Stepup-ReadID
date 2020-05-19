<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\Session\Model\SessionId;

final class ConfirmPendingSessionCommandHandler
{
    /** @var PendingSessionRepositoryInterface */
    private $pendingSessionRepository;

    public function __construct(PendingSessionRepositoryInterface $pendingSessionRepository)
    {
        $this->pendingSessionRepository = $pendingSessionRepository;
    }

    public function __invoke(ConfirmPendingSessionCommand $command): void
    {
        $readySessionId = ReadySessionId::fromString($command->readySessionId());
        $sessionId      = SessionId::fromString($command->sessionId());

        $pendingSession = $this->pendingSessionRepository->findOneByReadySession($readySessionId);

        $pendingSession->confirmSession($sessionId);

        $this->pendingSessionRepository->save($pendingSession);
    }
}
