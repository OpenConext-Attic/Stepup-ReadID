<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Services;

use PHPUnit\Framework\TestCase;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionAuthorizationException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionBadRequestException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Infrastructure\Services\ReadySession\HttpRequestReadySession;
use StepupReadId\Infrastructure\Services\ReadySession\ReadySessionClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpRequestReadySessionTest extends TestCase
{
    /** @var ReadySessionClientInterface */
    private $httpClient;
    /** @var ResponseInterface */
    private $response;

    protected function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(ReadySessionClientInterface::class)->getMock();
        $this->response   = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $this->httpClient
            ->expects($this->exactly(1))
            ->method('createReadySession')
            ->with(ReadySessionTTL::MINIMUM_TTL)
            ->willReturn($this->response);
    }

    public function testReceiveRequest(): void
    {
        $this->response
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_CREATED);

        $this->response
            ->expects($this->exactly(1))
            ->method('toArray')
            ->willReturn([
                HttpRequestReadySession::READY_SESSION_ID => '2e32c9a2-e34c-4f40-ac2f-dc47b67f3900',
                HttpRequestReadySession::BASE_64_QR => 'data:image/png;base64,0',
                HttpRequestReadySession::EXPIRY_TIMESTAMP => 1,
                HttpRequestReadySession::JWK_TOKEN => 'j.w.t',
            ]);

        $requestReadySession = new HttpRequestReadySession($this->httpClient);
        $readySession        = $requestReadySession->with(
            ReadySessionTTL::fromInteger(ReadySessionTTL::MINIMUM_TTL)
        );

        $this->assertEquals(
            ReadySession::create(
                ReadySessionId::fromString('2e32c9a2-e34c-4f40-ac2f-dc47b67f3900'),
                ReadySessionBase64Image::fromString('data:image/png;base64,0'),
                ReadySessionJwtToken::fromString('j.w.t'),
                ReadySessionTimestamp::fromInteger(1)
            ),
            $readySession
        );
    }

    public function testUnauthorizedRequest(): void
    {
        $this->expectException(RequestReadySessionAuthorizationException::class);

        $this->response
            ->expects($this->exactly(1))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_UNAUTHORIZED);

        $requestReadySession = new HttpRequestReadySession($this->httpClient);
        $requestReadySession->with(
            ReadySessionTTL::fromInteger(ReadySessionTTL::MINIMUM_TTL)
        );
    }

    public function testBadRequest(): void
    {
        $this->expectException(RequestReadySessionBadRequestException::class);

        $this->response
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $requestReadySession = new HttpRequestReadySession($this->httpClient);
        $requestReadySession->with(
            ReadySessionTTL::fromInteger(ReadySessionTTL::MINIMUM_TTL)
        );
    }
}
