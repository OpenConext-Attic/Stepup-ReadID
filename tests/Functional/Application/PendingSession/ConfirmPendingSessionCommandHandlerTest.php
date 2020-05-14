<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\PendingSession;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use StepupReadId\Application\PendingSession\ConfirmPendingSessionCommand;
use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Model\PendingSessionExpiryDate;
use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class ConfirmPendingSessionCommandHandlerTest extends KernelTestCase
{
    public function testConfirmPendingSession(): void
    {
        self::bootKernel();

        $container                = self::$container;
        $pendingSessionRepository = $container->get('test.' . PendingSessionRepositoryInterface::class);
        $messageBus               = $container->get('test.' . MessageBusInterface::class);

        $readySessionId = (string) Uuid::uuid4();
        $sessionId      = (string) Uuid::uuid4();

        $pendingSession = PendingSession::create(
            ReadySessionId::fromString($readySessionId),
            PendingSessionExpiryDate::fromTimestamp((new DateTimeImmutable('+3 hours'))->getTimestamp())
        );
        $pendingSessionRepository->save($pendingSession);

        $messageBus->dispatch(
            new ConfirmPendingSessionCommand($readySessionId, $sessionId)
        );

        $confirmedSession = $pendingSessionRepository
            ->findOneByReadySession(ReadySessionId::fromString($readySessionId));

        $this->assertTrue($confirmedSession->isConfirmed());
        $this->assertEquals($sessionId, $confirmedSession->sessionId()->value());
    }

    public function testExpiredPendindSession(): void
    {
        self::bootKernel();

        $container                = self::$container;
        $pendingSessionRepository = $container->get('test.' . PendingSessionRepositoryInterface::class);
        $messageBus               = $container->get('test.' . MessageBusInterface::class);

        $readySessionId = (string) Uuid::uuid4();
        $sessionId      = (string) Uuid::uuid4();

        $pendingSession = PendingSession::create(
            ReadySessionId::fromString($readySessionId),
            PendingSessionExpiryDate::fromTimestamp((new DateTimeImmutable('-3 hours'))->getTimestamp())
        );
        $pendingSessionRepository->save($pendingSession);

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('No pending session found with ReadySession id: ' . $readySessionId);

        $messageBus->dispatch(
            new ConfirmPendingSessionCommand($readySessionId, $sessionId)
        );
    }

    public function testNotFoundPendingSession(): void
    {
        self::bootKernel();

        $container  = self::$container;
        $messageBus = $container->get('test.' . MessageBusInterface::class);

        $readySessionId = (string) Uuid::uuid4();
        $sessionId      = (string) Uuid::uuid4();

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('No pending session found with ReadySession id: ' . $readySessionId);

        $messageBus->dispatch(
            new ConfirmPendingSessionCommand($readySessionId, $sessionId)
        );
    }
}
