<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use RuntimeException;
use function sprintf;

final class RequestReadySessionConnectionException extends RuntimeException
{
    public static function becauseTransportError(string $cause): self
    {
        return new self(sprintf('Transport error: %s', $cause));
    }

    public static function becauseResponseIsInvalid(): self
    {
        return new self('Invalid response');
    }
}
