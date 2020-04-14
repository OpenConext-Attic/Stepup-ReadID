<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class SessionId
{
    /** @var UuidInterface */
    private $value;

    private function __construct(UuidInterface $sessionId)
    {
        $this->value = $sessionId;
    }

    public static function fromString(string $sessionId): SessionId
    {
        return new self(Uuid::fromString($sessionId));
    }

    public function value(): string
    {
        return $this->value->toString();
    }

    public function equals(SessionId $other): bool
    {
        return $this->value->equals($other->value);
    }
}
