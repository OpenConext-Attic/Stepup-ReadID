<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

use Webmozart\Assert\Assert;

final class ReadySessionTimestamp
{
    /** @var int */
    private $value;

    private function __construct(int $timestamp)
    {
        Assert::greaterThan($timestamp, 0);

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
}
