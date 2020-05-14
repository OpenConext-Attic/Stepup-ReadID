<?php

declare(strict_types=1);

namespace StepupReadId\Domain\PendingSession\Exception;

use InvalidArgumentException;

class InvalidPendingSessionExpiryDate extends InvalidArgumentException
{
    public static function becauseNoValidISOString(string $expiryDateISO): self
    {
        return new self('Invalid ISO string :' . $expiryDateISO);
    }
}
