<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\ReadySession;

use StepupReadId\Application\ReadySession\CreateReadySessionCommand;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionBadRequestException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function json_encode;

class CreateReadySessionCommandHandlerTest extends KernelTestCase
{
    use HandleTrait;

    public function testRequestReadySession(): void
    {
        self::bootKernel();

        $container = self::$container;
        $container->set('test.' . HttpClientInterface::class, new MockHttpClient(new MockResponse(
            json_encode([
                'base64QR' => 'data:image/png;base64,0',
                'expiryTimestamp' => 1234567890,
                'jwtToken' => 'j.w.t',
                'readySessionID' => 'a0577ea3-1b40-433a-ad18-726c2a5378ad',
            ]),
            [
                'http_code' => Response::HTTP_CREATED,
            ]
        )));

        $this->messageBus = $container->get('test.' . MessageBusInterface::class);
        $response         = $this->handle(new CreateReadySessionCommand('opaqueId', ReadySessionTTL::MINIMUM_TTL));

        $this->assertInstanceOf(ReadySession::class, $response);
        $this->assertEquals($response->id()->value(), 'a0577ea3-1b40-433a-ad18-726c2a5378ad');
        $this->assertEquals($response->qrCode()->value(), 'data:image/png;base64,0');
        $this->assertEquals($response->jwtToken()->value(), 'j.w.t');
        $this->assertEquals($response->timestamp()->value(), 1234567890);
    }

    public function testUnauthorizedRequest(): void
    {
        self::bootKernel();

        $container = self::$container;
        $container->set('test.' . HttpClientInterface::class, new MockHttpClient(new MockResponse(
            json_encode([]),
            [
                'http_code' => Response::HTTP_UNAUTHORIZED,
            ]
        )));

        $this->messageBus = $container->get('test.' . MessageBusInterface::class);

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('Invalid authorization token');

        $this->handle(new CreateReadySessionCommand('opaqueId', ReadySessionTTL::MINIMUM_TTL));
    }

    public function testBadRequest(): void
    {
        $this->expectException(RequestReadySessionBadRequestException::class);

        self::bootKernel();

        $container = self::$container;
        $container->set('test.' . HttpClientInterface::class, new MockHttpClient(new MockResponse(
            json_encode([]),
            [
                'http_code' => Response::HTTP_BAD_REQUEST,
            ]
        )));

        $this->messageBus = $container->get('test.' . MessageBusInterface::class);

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('Server rejects create a new ReadySession with the error code \'400\'');

        $this->handle(new CreateReadySessionCommand('opaqueId', ReadySessionTTL::MINIMUM_TTL));
    }
}
