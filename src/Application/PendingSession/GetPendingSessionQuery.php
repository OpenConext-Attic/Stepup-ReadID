<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

final class GetPendingSessionQuery
{
    /** @var string */
    private $readySessionId;

    public function __construct(string $readySessionId)
    {
        $this->readySessionId = $readySessionId;
    }

    public function readySessionId(): string
    {
        return $this->readySessionId;
    }
}
