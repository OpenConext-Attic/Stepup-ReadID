<?php

declare(strict_types=1);

namespace StepupReadId\Domain\PendingSession\Services;

use StepupReadId\Domain\PendingSession\Exception\PendingSessionNotFoundException;
use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;

interface PendingSessionRepositoryInterface
{
    public function save(PendingSession $pendingSession): void;

    /**
     * @throws PendingSessionNotFoundException
     */
    public function findOneByReadySession(ReadySessionId $readySessionId): PendingSession;
}
