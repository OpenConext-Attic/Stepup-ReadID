<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Services;

use StepupReadId\Domain\ReadySession\Exception\ReadySessionNotFoundException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;

interface ReadySessionStateInterface
{
    public function save(ReadySession $readySession): void;

    /**
     * @throws ReadySessionNotFoundException
     */
    public function load(): ReadySession;
}
