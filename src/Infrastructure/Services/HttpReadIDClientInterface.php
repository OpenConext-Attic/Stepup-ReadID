<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface HttpReadIDClientInterface
{
    public function createReadySession(int $ttl): ResponseInterface;
}
