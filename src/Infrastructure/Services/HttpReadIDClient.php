<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class HttpReadIDClient implements HttpReadIDClientInterface
{
    private const CREATE_READY_SESSION_ENDPOINT_PATH = '/odata/v1/ODataServlet/createReadySession';

    /** @var HttpClientInterface */
    private $client;
    /** @var HttpReadIDConfiguration */
    private $readIDConfiguration;

    public function __construct(HttpClientInterface $client, HttpReadIDConfiguration $readIDConfiguration)
    {
        $this->client              = $client;
        $this->readIDConfiguration = $readIDConfiguration;
    }

    public function createReadySession(int $ttl): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $this->readIDConfiguration->endpoint() . self::CREATE_READY_SESSION_ENDPOINT_PATH,
            [
                'headers' => $this->getHeaders(),
                'json' => [
                    'opaqueID' => $this->readIDConfiguration->opaqueId(),
                    'TTL' => $ttl,
                ],
            ]
        );
    }

    /**
     * @return array<string, string>
     */
    private function getHeaders(): array
    {
        return [
            'X-Innovalor-Authorization' => $this->readIDConfiguration->authorizationToken(),
            'Content-Type' => 'application/json;odata.metadata=minimal',
            'Accept' => 'application/json;odata.metadata=minimal',
            'OData-MaxVersion' => '4.0',
            'OData-Version' => '4.0',
        ];
    }
}
