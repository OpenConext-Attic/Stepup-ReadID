<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadySession;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface ReadySessionClientInterface
{
    public function createReadySession(int $ttl): ResponseInterface;
}
