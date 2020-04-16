<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Model;

use StepupReadId\Domain\ReadySession\Model\ReadySessionId;

final class Session
{
    /** @var SessionId */
    private $id;
    /** @var SessionExpiryDate */
    private $expiryDate;
    /** @var ReadySessionId */
    private $readySessionId;

    private function __construct(
        SessionId $id,
        SessionExpiryDate $expiryDate,
        ReadySessionId $readySessionId
    ) {
        $this->id             = $id;
        $this->expiryDate     = $expiryDate;
        $this->readySessionId = $readySessionId;
    }

    public static function create(
        SessionId $id,
        SessionExpiryDate $expiryDate,
        ReadySessionId $readySessionId
    ): Session {
        return new self($id, $expiryDate, $readySessionId);
    }

    public function id(): SessionId
    {
        return $this->id;
    }

    public function expiryDate(): SessionExpiryDate
    {
        return $this->expiryDate;
    }

    public function readySessionId(): ReadySessionId
    {
        return $this->readySessionId;
    }
}
