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
    /** @var SessionDocument */
    private $sessionDocument;

    private function __construct(
        SessionId $id,
        SessionExpiryDate $expiryDate,
        SessionDocument $sessionDocument,
        ReadySessionId $readySessionId
    ) {
        $this->id              = $id;
        $this->expiryDate      = $expiryDate;
        $this->readySessionId  = $readySessionId;
        $this->sessionDocument = $sessionDocument;
    }

    public static function create(
        SessionId $id,
        SessionExpiryDate $expiryDate,
        SessionDocument $sessionDocument,
        ReadySessionId $readySessionId
    ): Session {
        return new self($id, $expiryDate, $sessionDocument, $readySessionId);
    }

    public function id(): SessionId
    {
        return $this->id;
    }

    public function expiryDate(): SessionExpiryDate
    {
        return $this->expiryDate;
    }

    public function document(): SessionDocument
    {
        return $this->sessionDocument;
    }

    public function readySessionId(): ReadySessionId
    {
        return $this->readySessionId;
    }
}
