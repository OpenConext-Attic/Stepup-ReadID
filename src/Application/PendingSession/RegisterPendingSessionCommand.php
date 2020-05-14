<?php

declare(strict_types=1);

namespace StepupReadId\Application\PendingSession;

final class RegisterPendingSessionCommand
{
    /** @var string */
    private $readySessionId;
    /** @var int */
    private $timestamp;

    public function __construct(
        string $readySessionId,
        int $timestamp
    ) {
        $this->readySessionId = $readySessionId;
        $this->timestamp      = $timestamp;
    }

    public function readySessionId(): string
    {
        return $this->readySessionId;
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }
}
