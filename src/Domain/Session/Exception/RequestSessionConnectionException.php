<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Exception;

use RuntimeException;
use StepupReadId\Domain\Session\Model\SessionId;
use function sprintf;

final class RequestSessionConnectionException extends RuntimeException
{
    public static function becauseUnauthorized(): self
    {
        return new self('Invalid authorization token');
    }

    public static function becauseNotFound(SessionId $sessionId): self
    {
        return new self(sprintf(
            'Session with id \'%s\' not found',
            $sessionId->value()
        ));
    }

    public static function becauseBadRequest(int $code): self
    {
        return new self(sprintf(
            'Server rejects session connection with the error code \'%s\'',
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
