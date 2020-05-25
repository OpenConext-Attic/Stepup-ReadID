<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\Session;

use DateTimeImmutable;
use StepupReadId\Application\Session\GetSessionQuery;
use StepupReadId\Domain\Session\Model\Session;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function json_encode;

class GetSessionQueryHandlerTest extends KernelTestCase
{
    use HandleTrait;

    public const SESSION_ID       = 'ce14bb4b-ea8d-4091-ba6b-5fa1e243f617';
    public const READY_SESSION_ID = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const EXPIRY_DATE      = '2000-01-01T00:00:00.00Z';

    public function testRequestSession(): void
    {
        self::bootKernel();

        $container = self::$container;
        $container->set('test.' . HttpClientInterface::class, new MockHttpClient(new MockResponse(
            json_encode([
                'sessionId' => self::SESSION_ID,
                'readySession' => ['readySessionId' => self::READY_SESSION_ID],
                'expiryDate' => self::EXPIRY_DATE,
                'documentContent' => ['attribute1' => 'value1'],
            ]),
            [
                'http_code' => Response::HTTP_OK,
            ]
        )));

        $this->messageBus = $container->get('test.' . MessageBusInterface::class);
        $response         = $this->handle(new GetSessionQuery(self::SESSION_ID));

        $this->assertInstanceOf(Session::class, $response);
        $this->assertEquals(self::SESSION_ID, $response->id()->value());
        $this->assertEquals(self::READY_SESSION_ID, $response->readySessionId()->value());
        $this->assertEquals(new DateTimeImmutable(self::EXPIRY_DATE), $response->expiryDate()->value());
        $this->assertTrue($response->document()->has('attribute1'));
    }
}
