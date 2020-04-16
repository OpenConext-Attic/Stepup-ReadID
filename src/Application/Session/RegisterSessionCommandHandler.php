<?php

declare(strict_types=1);

namespace StepupReadId\Application\Session;

use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionExpiryDate;
use StepupReadId\Domain\Session\Model\SessionId;
use StepupReadId\Domain\Session\Services\SessionRepositoryInterface;

final class RegisterSessionCommandHandler
{
    /** @var SessionRepositoryInterface */
    private $sessionRepository;

    public function __construct(SessionRepositoryInterface $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(RegisterSessionCommand $command): void
    {
        $session = Session::create(
            SessionId::fromString($command->id()),
            SessionExpiryDate::fromISOString($command->expiryDateISO()),
            ReadySessionId::fromString($command->readySessionId())
        );

        $this->sessionRepository->save($session);
    }
}
