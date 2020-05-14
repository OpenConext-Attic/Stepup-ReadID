<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\ReadySession;

use StepupReadId\Application\ReadySession\GetStoredReadySessionQuery;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Domain\ReadySession\Services\ReadySessionStateInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class GetStoredReadySessionQueryHandlerTest extends KernelTestCase
{
    use HandleTrait;

    /** @var ReadySessionStateInterface */
    private $readySessionState;

    public function setUp(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        $this->readySessionState = $container->get('test.' . ReadySessionStateInterface::class);
        $this->messageBus        = $container->get('test.' . MessageBusInterface::class);
    }

    public function testRecoverSavedReadySession(): void
    {
        $readySession = ReadySession::create(
            ReadySessionId::fromString('2e32c9a2-e34c-4f40-ac2f-dc47b67f3900'),
            ReadySessionBase64Image::fromString('data:image/png;base64,0'),
            ReadySessionJwtToken::fromString('j.w.t'),
            ReadySessionTimestamp::fromInteger(1)
        );
        $this->readySessionState->save($readySession);

        $storedReadySession = $this->handle(new GetStoredReadySessionQuery());

        self::assertEquals(
            $readySession,
            $storedReadySession
        );
    }

    public function testNoPreviousSavedReadySession(): void
    {
        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('No active ReadySession found in state.');

        $this->handle(new GetStoredReadySessionQuery());
    }
}
