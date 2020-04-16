<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Exception;

use InvalidArgumentException;

class InvalidSessionExpiryDate extends InvalidArgumentException
{
    public static function becauseNoValidISOString(string $expiryDateISO): self
    {
        return new self('Invalid ISO string :' . $expiryDateISO);
    }
}
