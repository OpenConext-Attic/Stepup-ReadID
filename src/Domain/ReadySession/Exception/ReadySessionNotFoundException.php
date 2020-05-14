<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Exception;

use RuntimeException;
use function sprintf;

class ReadySessionNotFoundException extends RuntimeException
{
    public static function stateIsEmpty(): ReadySessionNotFoundException
    {
        return new self(sprintf('No active ReadySession found in state.'));
    }
}
