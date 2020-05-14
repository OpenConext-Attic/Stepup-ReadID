<?php

declare(strict_types=1);

namespace StepupReadId\Domain\PendingSession\Model;

use DateTimeImmutable;

final class PendingSessionExpiryDate
{
    /** @var DateTimeImmutable */
    private $value;

    private function __construct(DateTimeImmutable $expiryDate)
    {
        $this->value = $expiryDate;
    }

    public static function fromTimestamp(int $timestamp): PendingSessionExpiryDate
    {
        $date = new DateTimeImmutable();

        return new self($date->setTimestamp($timestamp));
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function equals(PendingSessionExpiryDate $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }
}
