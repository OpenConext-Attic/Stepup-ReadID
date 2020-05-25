<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Services\Session;

use PHPUnit\Framework\TestCase;
use StepupReadId\Domain\Session\Exception\RequestSessionConnectionException;
use StepupReadId\Domain\Session\Model\SessionExpiryDate;
use StepupReadId\Domain\Session\Model\SessionId;
use StepupReadId\Infrastructure\Serializer\SessionNormalizer;
use StepupReadId\Infrastructure\Services\ReadId\ReadIdClientInterface;
use StepupReadId\Infrastructure\Services\Session\HttpRequestSession;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function json_encode;

class HttpRequestSessionTest extends TestCase
{
    public const SESSION_ID       = 'ce14bb4b-ea8d-4091-ba6b-5fa1e243f617';
    public const READY_SESSION_ID = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const EXPIRY_DATE      = '2000-01-01T00:00:00.00Z';

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
        $this->serializer = new Serializer([new SessionNormalizer()], [new JsonEncoder()]);

        $this->httpClient
            ->expects($this->exactly(1))
            ->method('get')
            ->with(
                HttpRequestSession::GET_SESSION_ENDPOINT_PATH,
                [self::SESSION_ID]
            )
            ->willReturn($this->response);
    }

    public function testReceiveRequest(): void
    {
        $this->response
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->response
            ->expects($this->exactly(1))
            ->method('getContent')
            ->willReturn(json_encode([
                'sessionId' => self::SESSION_ID,
                'readySession' => ['readySessionId' => self::READY_SESSION_ID],
                'expiryDate' => self::EXPIRY_DATE,
                'documentContent' => ['attribute1' => 'value1', 'attribute2' => 'value2'],
            ]));

        $requestSession = new HttpRequestSession($this->httpClient, $this->serializer);
        $session        = $requestSession->with(
            SessionId::fromString(self::SESSION_ID)
        );

        $this->assertEquals(self::SESSION_ID, $session->id()->value());
        $this->assertEquals(self::READY_SESSION_ID, $session->readySessionId()->value());
        $this->assertEquals('value1', $session->document()->get('attribute1')->value());
        $this->assertEquals('value2', $session->document()->get('attribute2')->value());
        $this->assertTrue($session->expiryDate()->equals(
            SessionExpiryDate::fromISOString(self::EXPIRY_DATE)
        ));
    }

    public function testUnauthorizedRequest(): void
    {
        $this->expectException(RequestSessionConnectionException::class);
        $this->expectExceptionMessage('Invalid authorization token');

        $this->response
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_UNAUTHORIZED);

        $requestSession = new HttpRequestSession($this->httpClient, $this->serializer);
        $requestSession->with(
            SessionId::fromString(self::SESSION_ID)
        );
    }

    public function testBadRequest(): void
    {
        $this->expectException(RequestSessionConnectionException::class);
        $this->expectExceptionMessage('Server rejects session connection with the error code \'400\'');

        $this->response
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $requestSession = new HttpRequestSession($this->httpClient, $this->serializer);
        $requestSession->with(
            SessionId::fromString(self::SESSION_ID)
        );
    }

    public function testNotFound(): void
    {
        $this->expectException(RequestSessionConnectionException::class);
        $this->expectExceptionMessage('Session with id \'' . self::SESSION_ID . '\' not found');

        $this->response
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_NOT_FOUND);

        $requestSession = new HttpRequestSession($this->httpClient, $this->serializer);
        $requestSession->with(
            SessionId::fromString(self::SESSION_ID)
        );
    }
}
