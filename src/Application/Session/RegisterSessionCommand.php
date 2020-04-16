<?php

declare(strict_types=1);

namespace StepupReadId\Application\Session;

final class RegisterSessionCommand
{
    /** @var string */
    private $id;
    /** @var string */
    private $expiryDateISO;
    /** @var string */
    private $readySessionId;

    public function __construct(
        string $id,
        string $expiryDateISO,
        string $readySessionId
    ) {
        $this->id             = $id;
        $this->expiryDateISO  = $expiryDateISO;
        $this->readySessionId = $readySessionId;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function readySessionId(): string
    {
        return $this->readySessionId;
    }

    public function expiryDateISO(): string
    {
        return $this->expiryDateISO;
    }
}
