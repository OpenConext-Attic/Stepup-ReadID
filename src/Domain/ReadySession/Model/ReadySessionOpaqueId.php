<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

use StepupReadId\Domain\ReadySession\Exception\InvalidReadySessionOpaqueIdException;

final class ReadySessionOpaqueId
{
    /** @var string */
    private $value;

    private function __construct(string $opaqueId)
    {
        $this->checkValidOpaqueId($opaqueId);

        $this->value = $opaqueId;
    }

    public static function fromString(string $opaqueId): ReadySessionOpaqueId
    {
        return new self($opaqueId);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ReadySessionOpaqueId $other): bool
    {
        return $this->value === $other->value;
    }

    private function checkValidOpaqueId(string $opaqueId): void
    {
        if (empty($opaqueId)) {
            throw InvalidReadySessionOpaqueIdException::becauseEmpty();
        }
    }
}
