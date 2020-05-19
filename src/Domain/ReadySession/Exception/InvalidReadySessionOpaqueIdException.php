<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use InvalidArgumentException;

final class InvalidReadySessionOpaqueIdException extends InvalidArgumentException
{
    public static function becauseEmpty(): InvalidReadySessionOpaqueIdException
    {
        return new self('Opaque id is empty');
    }
}
