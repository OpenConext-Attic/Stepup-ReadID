<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadId;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface ReadIdClientInterface
{
    /**
     * @param array<string> $args
     */
    public function get(string $url, array $args = []): ResponseInterface;

    /**
     * @param array<string, mixed> $body
     * @param array<string>        $args
     */
    public function post(string $url, array $body, array $args = []): ResponseInterface;
}
