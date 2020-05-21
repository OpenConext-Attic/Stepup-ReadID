<?php

declare(strict_types=1);

namespace StepupReadId\Application\Session;

use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionId;
use StepupReadId\Domain\Session\Services\RequestSessionInterface;

final class GetSessionQueryHandler
{
    /** @var RequestSessionInterface */
    private $requestSession;

    public function __construct(RequestSessionInterface $requestSession)
    {
        $this->requestSession = $requestSession;
    }

    public function __invoke(GetSessionQuery $query): Session
    {
        $sessionId = SessionId::fromString($query->sessionId());

        return $this->requestSession->with($sessionId);
    }
}
