<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Domain\ReadySession\Services\RequestReadySessionInterface;

final class GetReadySessionQueryHandler
{
    /** @var RequestReadySessionInterface */
    private $requestReadySession;

    public function __construct(RequestReadySessionInterface $requestReadySession)
    {
        $this->requestReadySession = $requestReadySession;
    }

    public function __invoke(GetReadySessionQuery $query): ReadySession
    {
        $ttl = ReadySessionTTL::fromInteger($query->ttl());

        return $this->requestReadySession->with($ttl);
    }
}
