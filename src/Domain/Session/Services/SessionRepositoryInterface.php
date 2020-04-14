<?php

declare(strict_types=1);

namespace StepupReadId\Domain\Session\Services;

use StepupReadId\Domain\Session\Model\Session;

interface SessionRepositoryInterface
{
    public function save(Session $session): void;
}
