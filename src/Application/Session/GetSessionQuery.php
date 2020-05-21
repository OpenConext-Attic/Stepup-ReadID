<?php

declare(strict_types=1);

namespace StepupReadId\Application\Session;

final class GetSessionQuery
{
    /** @var string */
    private $sessionId;

    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }
}
