<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\ReadySession;

use StepupReadId\Application\ReadySession\GetReadySessionQuery;
use StepupReadId\Application\ReadySession\GetReadySessionQueryHandler;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionAuthorizationException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionBadRequestException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function json_encode;

class GetReadySessionQueryHandlerTest extends KernelTestCase
{
    /** @var GetReadySessionQueryHandler */
    private static $getReadySessionQueryHandler;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $container->set('test.' . HttpClientInterface::class, new MockHttpClient(static::responses()));

        static::$getReadySessionQueryHandler = $container->get('test.' . GetReadySessionQueryHandler::class);
    }

    public function testRequestReadySession(): void
    {
        $response = (static::$getReadySessionQueryHandler)(new GetReadySessionQuery(ReadySessionTTL::MINIMUM_TTL));

        $this->assertInstanceOf(ReadySession::class, $response);
        $this->assertEquals($response->id()->value(), 'a0577ea3-1b40-433a-ad18-726c2a5378ad');
        $this->assertEquals($response->qrCode()->value(), 'data:image/png;base64,0');
        $this->assertEquals($response->jwtToken()->value(), 'j.w.t');
        $this->assertEquals($response->timestamp()->value(), 1234567890);
    }

    public function testUnauthorizedRequest(): void
    {
        $this->expectException(RequestReadySessionAuthorizationException::class);

        (static::$getReadySessionQueryHandler)(new GetReadySessionQuery(ReadySessionTTL::MINIMUM_TTL));
    }

    public function testBadRequest(): void
    {
        $this->expectException(RequestReadySessionBadRequestException::class);

        (static::$getReadySessionQueryHandler)(new GetReadySessionQuery(ReadySessionTTL::MINIMUM_TTL));
    }

    /**
     * @return array<MockResponse>
     */
    private static function responses(): array
    {
        return [
            // Good request
            new MockResponse(
                json_encode([
                    'base64QR' => 'data:image/png;base64,0',
                    'expiryTimestamp' => 1234567890,
                    'jwtToken' => 'j.w.t',
                    'readySessionID' => 'a0577ea3-1b40-433a-ad18-726c2a5378ad',
                ]),
                [
                    'http_code' => Response::HTTP_CREATED,
                ]
            ),
            // Unauthorized request
            new MockResponse(
                json_encode([]),
                [
                    'http_code' => Response::HTTP_UNAUTHORIZED,
                ]
            ),
            // Bad request
            new MockResponse(
                json_encode([]),
                [
                    'http_code' => Response::HTTP_BAD_REQUEST,
                ]
            ),
        ];
    }
}
