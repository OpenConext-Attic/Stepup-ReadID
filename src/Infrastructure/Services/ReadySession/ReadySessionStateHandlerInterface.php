<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadySession;

use StepupReadId\Domain\ReadySession\Model\ReadySession;
use Surfnet\GsspBundle\Exception\NotFound;

interface ReadySessionStateHandlerInterface
{
    public function saveReadySession(ReadySession $readySession): void;

    /**
     * @throws NotFound
     */
    public function getReadySession(): ReadySession;
}
