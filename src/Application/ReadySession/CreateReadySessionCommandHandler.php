<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionOpaqueId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Domain\ReadySession\Services\ReadySessionStateInterface;
use StepupReadId\Domain\ReadySession\Services\RequestReadySessionInterface;

final class CreateReadySessionCommandHandler
{
    /** @var RequestReadySessionInterface */
    private $requestReadySession;
    /** @var ReadySessionStateInterface */
    private $readySessionState;

    public function __construct(
        ReadySessionStateInterface $readySessionState,
        RequestReadySessionInterface $requestReadySession
    ) {
        $this->readySessionState   = $readySessionState;
        $this->requestReadySession = $requestReadySession;
    }

    public function __invoke(CreateReadySessionCommand $query): ReadySession
    {
        $ttl      = ReadySessionTTL::fromInteger($query->ttl());
        $opaqueId = ReadySessionOpaqueId::fromString($query->opaqueId());

        $readySession = $this->requestReadySession->with($opaqueId, $ttl);

        $this->readySessionState->save($readySession);

        return $readySession;
    }
}
