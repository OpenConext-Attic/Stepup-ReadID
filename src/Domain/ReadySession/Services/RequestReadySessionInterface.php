<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Services;

use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionConnectionException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionOpaqueId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;

/**
 * @throws RequestReadySessionConnectionException
 */
interface RequestReadySessionInterface
{
    public function with(ReadySessionOpaqueId $opaqueId, ReadySessionTTL $qrCodeTtl): ReadySession;
}
