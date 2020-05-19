<?php

declare(strict_types=1);

namespace StepupReadId\Application\ReadySession;

use StepupReadId\Domain\ReadySession\Exception\ReadySessionNotFoundException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Services\ReadySessionStateInterface;

final class GetStoredReadySessionQueryHandler
{
    /** @var ReadySessionStateInterface */
    private $readySessionReposity;

    public function __construct(ReadySessionStateInterface $readySessionReposity)
    {
        $this->readySessionReposity = $readySessionReposity;
    }

    /**
     * @throws ReadySessionNotFoundException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(GetStoredReadySessionQuery $query): ReadySession
    {
        return $this->readySessionReposity->load();
    }
}
