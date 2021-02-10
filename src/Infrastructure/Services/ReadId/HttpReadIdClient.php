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
        $argjson=json_encode($args);
        syslog(LOG_INFO, sprintf(
            'ReadId::get("%s", %s)',
            $url, $argjson
            )
        );
        /** @var ResponseInterface */
        $res=$this->client->request(
            Request::METHOD_GET,
            $this->getEndpoint($url, $args),
            [
                'headers' => $this->authorizationHeader(),
            ]
        );
        /** @var int */
        $statusCode=$res->getStatusCode();
        /** @var string */
        $content=$res->getContent(false);
        syslog(LOG_INFO, sprintf(
                'ReadId::get ==> StatusCode=%u; Content="%s"',
                $statusCode,
                $content
            )
        );

        return $res;
    }

    /**
     * @inheritDoc
     */
    public function post(string $url, array $body, array $args = []): ResponseInterface
    {
        $bodyjson=json_encode($body);
        $argjson=json_encode($args);
        syslog(LOG_INFO, sprintf(
                'ReadId::post("%s", %s, %s)',
                $url, $bodyjson, $argjson
            )
        );
        /** @var ResponseInterface */
        $res=$this->client->request(
            Request::METHOD_POST,
            $this->getEndpoint($url, $args),
            [
                'headers' => $this->authorizationHeader(),
                'json' => $body,
            ]
        );
        /** @var int */
        $statusCode=$res->getStatusCode();
        /** @var string */
        $content=$res->getContent(false);
        syslog(LOG_INFO, sprintf(
                'ReadId::post ==> StatusCode=%u; Content="%s"',
                $statusCode,
                $content
            )
        );

        return $res;
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
