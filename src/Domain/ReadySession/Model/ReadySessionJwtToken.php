<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

final class ReadySessionJwtToken
{
    /** @var string */
    private $value;

    private function __construct(string $jwtToken)
    {
        $this->value = $jwtToken;
    }

    public static function fromString(string $jwtToken): self
    {
        return new self($jwtToken);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ReadySessionJwtToken $other): bool
    {
        return $this->value === $other->value;
    }
}
