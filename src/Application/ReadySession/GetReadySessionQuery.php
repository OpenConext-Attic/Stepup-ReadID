<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;

final class GetReadySessionQuery
{
    /** @var int */
    private $ttl;

    public function __construct(int $ttl)
    {
        $this->ttl = $ttl;
    }

    public static function withMinimumTTL(): GetReadySessionQuery
    {
        return new self(ReadySessionTTL::MINIMUM_TTL);
    }

    public function ttl(): int
    {
        return $this->ttl;
    }
}
