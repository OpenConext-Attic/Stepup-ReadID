<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use InvalidArgumentException;

final class InvalidReadySessionBase64ImageException extends InvalidArgumentException
{
    public static function becauseNoValidHeader(): self
    {
        return new self('Invalid base64 image header');
    }
}
