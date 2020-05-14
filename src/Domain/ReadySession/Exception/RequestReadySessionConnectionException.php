<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use RuntimeException;
use function sprintf;

final class RequestReadySessionConnectionException extends RuntimeException
{
    public static function becauseUnauthorized(): self
    {
        return new self('Invalid authorization token');
    }

    public static function becauseBadRequest(int $code): self
    {
        return new self(sprintf(
            'Server rejects create a new ReadySession with the error code \'%s\'',
            $code
        ));
    }

    public static function becauseTransportError(string $cause): self
    {
        return new self(sprintf('Transport error: %s', $cause));
    }

    public static function becauseResponseIsInvalid(): self
    {
        return new self('Invalid response');
    }
}
