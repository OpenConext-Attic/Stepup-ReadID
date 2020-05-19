<?php

declare(strict_types=1);

namespace StepupReadId\Domain\PendingSession\Exception;

use RuntimeException;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use function sprintf;

final class PendingSessionNotFoundException extends RuntimeException
{
    public static function withReadySessionId(ReadySessionId $readySessionId): PendingSessionNotFoundException
    {
        return new self(sprintf('No pending session found with ReadySession id: %s', $readySessionId->value()));
    }
}
