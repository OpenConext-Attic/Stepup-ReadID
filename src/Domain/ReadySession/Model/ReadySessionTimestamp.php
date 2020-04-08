<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

use StepupReadId\Domain\ReadySession\Exception\InvalidReadySessionTimestampException;

final class ReadySessionTimestamp
{
    /** @var int */
    private $value;

    private function __construct(int $timestamp)
    {
        $this->checkValidTimestamp($timestamp);

        $this->value = $timestamp;
    }

    public static function fromInteger(int $timestamp): ReadySessionTimestamp
    {
        return new self($timestamp);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ReadySessionTimestamp $other): bool
    {
        return $this->value === $other->value;
    }

    private function checkValidTimestamp(int $timestamp): void
    {
        if ($timestamp <= 0) {
            throw InvalidReadySessionTimestampException::becauseShouldBeGreaterThanZero();
        }
    }
}
