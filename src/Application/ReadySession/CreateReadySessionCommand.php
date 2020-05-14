<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

final class CreateReadySessionCommand
{
    /** @var string */
    private $opaqueId;
    /** @var int */
    private $ttl;

    public function __construct(string $opaqueId, int $ttl)
    {
        $this->opaqueId = $opaqueId;
        $this->ttl      = $ttl;
    }

    public function opaqueId(): string
    {
        return $this->opaqueId;
    }

    public function ttl(): int
    {
        return $this->ttl;
    }
}
