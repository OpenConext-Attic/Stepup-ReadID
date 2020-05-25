<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Services;

use StepupReadId\Domain\Session\Exception\RequestSessionConnectionException;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionId;

interface RequestSessionInterface
{
    /**
     * @throws RequestSessionConnectionException
     */
    public function with(SessionId $sessionId): Session;
}
