<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\PendingSession;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use StepupReadId\Application\PendingSession\RegisterPendingSessionCommand;
use StepupReadId\Application\PendingSession\RegisterPendingSessionCommandHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function sprintf;

class RegisterPendingSessionCommandHandlerTest extends KernelTestCase
{
    public const SESSION_ID       = 'cb82582e-da8c-476b-9fd5-dd2b63a20013';
    public const READY_SESSION_ID = '8297453c-b936-4396-a5ab-1436a5499f10';

    /** @var RegisterPendingSessionCommandHandler */
    private static $registerSessionCommandHandler;
    /** @var CacheItemPoolInterface */
    private static $cacheItemPool;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        static::$registerSessionCommandHandler = $container->get('test.' . RegisterPendingSessionCommandHandler::class);
        static::$cacheItemPool                 = $container->get('cache.app');
    }

    public function testRegisterSession(): void
    {
        $expiryDate             = new DateTimeImmutable('+3 hours');
        $registerSessionCommand = new RegisterPendingSessionCommand(
            self::READY_SESSION_ID,
            $expiryDate->getTimestamp()
        );

        static::$registerSessionCommandHandler->__invoke($registerSessionCommand);

        $item = self::$cacheItemPool->getItem('ready_session_id_' . self::READY_SESSION_ID)->get();

        $this->assertJsonStringEqualsJsonString(
            $this->getFormattedJsonResponse($expiryDate),
            $item
        );
    }

    public function testRegisterExpiredSession(): void
    {
        $expiryDate             = new DateTimeImmutable('-3 hours');
        $registerSessionCommand = new RegisterPendingSessionCommand(
            self::READY_SESSION_ID,
            $expiryDate->getTimestamp()
        );

        static::$registerSessionCommandHandler->__invoke($registerSessionCommand);

        $item = self::$cacheItemPool->getItem('ready_session_id_' . self::READY_SESSION_ID);

        $this->assertFalse($item->isHit());
    }

    private function getFormattedJsonResponse(DateTimeImmutable $expiryDate): string
    {
        return sprintf(
            '{"readySessionId":"%s","expiryDate":%d,"sessionId":null}',
            self::READY_SESSION_ID,
            $expiryDate->getTimestamp()
        );
    }
}
