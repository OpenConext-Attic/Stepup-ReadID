<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Services;

use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionAuthorizationException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionBadRequestException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionConnectionException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;

/**
 * @throws RequestReadySessionAuthorizationException
 * @throws RequestReadySessionBadRequestException
 * @throws RequestReadySessionConnectionException
 */
interface RequestReadySessionInterface
{
    public function with(ReadySessionTTL $ttl): ReadySession;
}
