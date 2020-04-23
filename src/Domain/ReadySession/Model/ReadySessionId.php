<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ReadySessionId
{
    /** @var UuidInterface */
    private $value;

    private function __construct(UuidInterface $readySessionId)
    {
        $this->value = $readySessionId;
    }

    public static function fromString(string $readySessionId): ReadySessionId
    {
        return new self(Uuid::fromString($readySessionId));
    }

    public function value(): string
    {
        return $this->value->toString();
    }

    public function equals(ReadySessionId $other): bool
    {
        return $this->value->equals($other->value);
    }
}
