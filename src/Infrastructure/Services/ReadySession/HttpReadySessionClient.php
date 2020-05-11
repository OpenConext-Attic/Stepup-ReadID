<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadySession;

use StepupReadId\Infrastructure\Services\ReadIdConfiguration;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function sprintf;

final class HttpReadySessionClient implements ReadySessionClientInterface
{
    private const CREATE_READY_SESSION_ENDPOINT_PATH = '/odata/v1/ODataServlet/createReadySession';

    /** @var HttpClientInterface */
    private $client;
    /** @var ReadIdConfiguration */
    private $readIDConfiguration;

    public function __construct(HttpClientInterface $client, ReadIdConfiguration $readIDConfiguration)
    {
        $this->client              = $client;
        $this->readIDConfiguration = $readIDConfiguration;
    }

    public function createReadySession(int $ttl): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $this->getEndpoint(self::CREATE_READY_SESSION_ENDPOINT_PATH),
            [
                'headers' => $this->getHeaders(),
                'json' => [
                    'opaqueID' => $this->readIDConfiguration->opaqueId(),
                    'TTL' => $ttl,
                ],
            ]
        );
    }

    private function getEndpoint(string $path): string
    {
        return sprintf('https://%s/%s', $this->readIDConfiguration->readIdServerFqdn(), $path);
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
