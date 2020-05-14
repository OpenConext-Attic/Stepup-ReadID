<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

final class ConfirmPendingSessionCommand
{
    /** @var string */
    private $readySessionId;
    /** @var string */
    private $sessionId;

    public function __construct(
        string $readySessionId,
        string $sessionId
    ) {
        $this->readySessionId = $readySessionId;
        $this->sessionId      = $sessionId;
    }

    public function readySessionId(): string
    {
        return $this->readySessionId;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }
}
