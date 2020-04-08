<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use InvalidArgumentException;

final class InvalidReadySessionTimestampException extends InvalidArgumentException
{
    public static function becauseShouldBeGreaterThanZero(): self
    {
        return new self('Timestamp must be greater than zero.');
    }
}
