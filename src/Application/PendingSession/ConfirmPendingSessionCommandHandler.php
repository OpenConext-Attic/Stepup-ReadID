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

        syslog(LOG_INFO, sprintf(
            'Searching for pending session "%s"',
            $readySessionId
            )
        );
        $pendingSession = $this->pendingSessionRepository->findOneByReadySession($readySessionId);
        $sessionID=$pendingSession->sessionId();
        syslog(LOG_INFO, sprintf(
                'Found pending session with ID "%s", confirming...',
                $sessionID
            )
        );

        $pendingSession->confirmSession($sessionId);
        syslog(LOG_INFO, 'Confirmed session');

        $this->pendingSessionRepository->save($pendingSession);
        syslog(LOG_INFO, 'Saved session');
    }
}
