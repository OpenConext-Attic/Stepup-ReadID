<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

use StepupReadId\Domain\ReadySession\Exception\InvalidReadySessionTTLException;

final class ReadySessionTTL
{
    public const MINIMUM_TTL = 30;
    public const MAXIMUM_TTL = 72000;

    /** @var int */
    private $value;

    private function __construct(int $ttl)
    {
        if ($ttl < self::MINIMUM_TTL || $ttl > self::MAXIMUM_TTL) {
            throw InvalidReadySessionTTLException::becauseOutOfRange($ttl);
        }

        $this->value = $ttl;
    }

    public static function fromInteger(int $ttl): self
    {
        return new self($ttl);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ReadySessionTTL $other): bool
    {
        return $this->value === $other->value;
    }
}
