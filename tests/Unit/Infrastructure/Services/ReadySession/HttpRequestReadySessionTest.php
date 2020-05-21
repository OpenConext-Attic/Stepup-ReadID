<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Services\ReadySession;

use PHPUnit\Framework\TestCase;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionConnectionException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionOpaqueId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Infrastructure\Serializer\ReadySessionNormalizer;
use StepupReadId\Infrastructure\Services\ReadId\ReadIdClientInterface;
use StepupReadId\Infrastructure\Services\ReadySession\HttpRequestReadySession;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function json_encode;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HttpRequestReadySessionTest extends TestCase
{
    /** @var ReadIdClientInterface */
    private $httpClient;
    /** @var ResponseInterface */
    private $response;
    /** @var Serializer */
    private $serializer;

    protected function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(ReadIdClientInterface::class)->getMock();
        $this->response   = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->serializer = new Serializer([new ReadySessionNormalizer()], [new JsonEncoder()]);

        $this->httpClient
            ->expects($this->exactly(1))
            ->method('post')
            ->with(
                HttpRequestReadySession::CREATE_READY_SESSION_ENDPOINT_PATH,
                [
                    'opaqueID' => 'opaque_id',
                    'TTL' => ReadySessionTTL::MINIMUM_TTL,
                ]
            )
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
            ->method('getContent')
            ->willReturn(json_encode([
                ReadySessionNormalizer::READY_SESSION_ID => '2e32c9a2-e34c-4f40-ac2f-dc47b67f3900',
                ReadySessionNormalizer::BASE_64_QR => 'data:image/png;base64,0',
                ReadySessionNormalizer::EXPIRY_TIMESTAMP => 1,
                ReadySessionNormalizer::JWK_TOKEN => 'j.w.t',
            ]));

        $requestReadySession = new HttpRequestReadySession($this->httpClient, $this->serializer);
        $readySession        = $requestReadySession->with(
            ReadySessionOpaqueId::fromString('opaque_id'),
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
        $this->expectException(RequestReadySessionConnectionException::class);
        $this->expectExceptionMessage('Invalid authorization token');

        $this->response
            ->expects($this->exactly(1))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_UNAUTHORIZED);

        $requestReadySession = new HttpRequestReadySession($this->httpClient, $this->serializer);
        $requestReadySession->with(
            ReadySessionOpaqueId::fromString('opaque_id'),
            ReadySessionTTL::fromInteger(ReadySessionTTL::MINIMUM_TTL)
        );
    }

    public function testBadRequest(): void
    {
        $this->expectException(RequestReadySessionConnectionException::class);
        $this->expectExceptionMessage('Server rejects create a new ReadySession with the error code \'400\'');

        $this->response
            ->expects($this->exactly(3))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $requestReadySession = new HttpRequestReadySession($this->httpClient, $this->serializer);
        $requestReadySession->with(
            ReadySessionOpaqueId::fromString('opaque_id'),
            ReadySessionTTL::fromInteger(ReadySessionTTL::MINIMUM_TTL)
        );
    }
}
