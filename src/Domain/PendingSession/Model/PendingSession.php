<?php

declare(strict_types=1);

namespace StepupReadId\Domain\PendingSession\Model;

use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\Session\Model\SessionId;

final class PendingSession
{
    /** @var ReadySessionId */
    private $readySessionId;
    /** @var PendingSessionExpiryDate */
    private $pendingSessionExpiryDate;
    /** @var SessionId|null */
    private $sessionId;

    private function __construct(
        ReadySessionId $readySessionId,
        PendingSessionExpiryDate $pendingSessionExpiryDate,
        ?SessionId $sessionId
    ) {
        $this->readySessionId           = $readySessionId;
        $this->pendingSessionExpiryDate = $pendingSessionExpiryDate;
        $this->sessionId                = $sessionId;
    }

    public static function create(
        ReadySessionId $readySessionId,
        PendingSessionExpiryDate $pendingSessionExpiryDate
    ): PendingSession {
        return new self($readySessionId, $pendingSessionExpiryDate, null);
    }

    public static function createWithSession(
        ReadySessionId $readySessionId,
        PendingSessionExpiryDate $pendingSessionExpiryDate,
        SessionId $sessionId
    ): PendingSession {
        return new self($readySessionId, $pendingSessionExpiryDate, $sessionId);
    }

    public function readySessionId(): ReadySessionId
    {
        return $this->readySessionId;
    }

    public function expiryDate(): PendingSessionExpiryDate
    {
        return $this->pendingSessionExpiryDate;
    }

    public function confirmSession(SessionId $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function isConfirmed(): bool
    {
        return $this->sessionId() instanceof SessionId;
    }

    public function sessionId(): ?SessionId
    {
        return $this->sessionId;
    }
}
