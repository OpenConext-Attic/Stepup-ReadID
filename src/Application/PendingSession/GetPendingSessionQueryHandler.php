<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;

final class GetPendingSessionQueryHandler
{
    /** @var PendingSessionRepositoryInterface */
    private $pendingSessionRepository;

    public function __construct(PendingSessionRepositoryInterface $pendingSessionRepository)
    {
        $this->pendingSessionRepository = $pendingSessionRepository;
    }

    public function __invoke(GetPendingSessionQuery $query): PendingSession
    {
        $readySessionId = ReadySessionId::fromString($query->readySessionId());

        return $this->pendingSessionRepository->findOneByReadySession($readySessionId);
    }
}
