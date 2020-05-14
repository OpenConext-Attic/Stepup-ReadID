<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadId;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function sprintf;

final class HttpReadIdClient implements ReadIdClientInterface
{
    /** @var HttpClientInterface */
    private $client;
    /** @var string */
    private $readIdServerFqdn;
    /** @var string */
    private $authorizationToken;

    public function __construct(HttpClientInterface $client, string $readIdServerFqdn, string $authorizationToken)
    {
        $this->client             = $client;
        $this->readIdServerFqdn   = $readIdServerFqdn;
        $this->authorizationToken = $authorizationToken;
    }

    /**
     * @inheritDoc
     */
    public function get(string $url, array $args = []): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_GET,
            $this->getEndpoint($url, $args),
            [
                'headers' => $this->authorizationHeader(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, array $body, array $args = []): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_POST,
            $this->getEndpoint($url, $args),
            [
                'headers' => $this->authorizationHeader(),
                'json' => $body,
            ]
        );
    }

    /**
     * @param array<string> $args
     */
    private function getEndpoint(string $path, array $args = []): string
    {
        $parsedPath = sprintf($path, ...$args);

        return sprintf('https://%s/%s', $this->readIdServerFqdn, $parsedPath);
    }

    /**
     * @return array<string, string>
     */
    private function authorizationHeader(): array
    {
         return [
             'X-Innovalor-Authorization' => $this->authorizationToken,
             'Content-Type' => 'application/json;odata.metadata=minimal',
             'Accept' => 'application/json;odata.metadata=minimal',
             'OData-MaxVersion' => '4.0',
             'OData-Version' => '4.0',
         ];
    }
}
