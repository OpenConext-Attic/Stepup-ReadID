<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\PendingSession;

use DateTime;
use Ramsey\Uuid\Uuid;
use StepupReadId\Application\PendingSession\GetPendingSessionQuery;
use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Model\PendingSessionExpiryDate;
use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use function sprintf;

class GetPendingSessionQueryHandlerTest extends KernelTestCase
{
    use HandleTrait;

    public function testGetPendingSession(): void
    {
        self::bootKernel();

        $container = self::$container;

        $readySessionId = (string) Uuid::uuid4();
        $timestamp      = (new DateTime('+3 hours'))->getTimestamp();

        $pendingSessionRepository = $container->get('test.' . PendingSessionRepositoryInterface::class);
        $this->messageBus         = $container->get('test.' . MessageBusInterface::class);

        $pendingSession = PendingSession::create(
            ReadySessionId::fromString($readySessionId),
            PendingSessionExpiryDate::fromTimestamp($timestamp)
        );
        $pendingSessionRepository->save(
            $pendingSession
        );

        $foundPendingSession = $this->handle(new GetPendingSessionQuery($readySessionId));

        $this->assertEquals($pendingSession, $foundPendingSession);
    }

    public function testNotFoundPendingSession(): void
    {
        self::bootKernel();

        $container = self::$container;

        $readySessionId = (string) Uuid::uuid4();

        $this->messageBus = $container->get('test.' . MessageBusInterface::class);

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage(sprintf('No pending session found with ReadySession id: %s', $readySessionId));

        $this->handle(new GetPendingSessionQuery($readySessionId));
    }
}
