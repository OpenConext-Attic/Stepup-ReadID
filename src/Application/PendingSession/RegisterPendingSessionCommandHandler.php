<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Model\PendingSessionExpiryDate;
use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;

final class RegisterPendingSessionCommandHandler
{
    /** @var PendingSessionRepositoryInterface */
    private $pendingSessionRepository;

    public function __construct(PendingSessionRepositoryInterface $pendingSessionRepository)
    {
        $this->pendingSessionRepository = $pendingSessionRepository;
    }

    public function __invoke(RegisterPendingSessionCommand $command): void
    {
        $pendingSession = PendingSession::create(
            ReadySessionId::fromString($command->readySessionId()),
            PendingSessionExpiryDate::fromTimestamp($command->timestamp())
        );

        $this->pendingSessionRepository->save($pendingSession);
    }
}
