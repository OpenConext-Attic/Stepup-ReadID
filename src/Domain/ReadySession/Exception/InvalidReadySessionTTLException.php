<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use InvalidArgumentException;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use function sprintf;

final class InvalidReadySessionTTLException extends InvalidArgumentException
{
    public static function becauseOutOfRange(int $ttl): self
    {
        return new self(
            sprintf(
                'Invalid TTL (%d), must be between %d and %d',
                $ttl,
                ReadySessionTTL::MINIMUM_TTL,
                ReadySessionTTL::MINIMUM_TTL
            )
        );
    }
}
