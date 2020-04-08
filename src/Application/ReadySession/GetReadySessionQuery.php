<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

final class GetReadySessionQuery
{
    /** @var int */
    private $ttl;

    public function __construct(int $ttl)
    {
        $this->ttl = $ttl;
    }

    public function ttl(): int
    {
        return $this->ttl;
    }
}
