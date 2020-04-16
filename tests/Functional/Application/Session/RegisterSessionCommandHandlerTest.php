<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\Session;

use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use StepupReadId\Application\Session\RegisterSessionCommand;
use StepupReadId\Application\Session\RegisterSessionCommandHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function sprintf;
use const DATE_RFC3339;

class RegisterSessionCommandHandlerTest extends KernelTestCase
{
    public const SESSION_ID       = 'cb82582e-da8c-476b-9fd5-dd2b63a20013';
    public const READY_SESSION_ID = '8297453c-b936-4396-a5ab-1436a5499f10';

    /** @var RegisterSessionCommandHandler */
    private static $registerSessionCommandHandler;
    /** @var CacheItemPoolInterface */
    private static $cacheItemPool;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        static::$registerSessionCommandHandler = $container->get('test.' . RegisterSessionCommandHandler::class);
        static::$cacheItemPool                 = $container->get('cache.app');
    }

    public function testRegisterSession(): void
    {
        $expiryDate             = new DateTimeImmutable('+3 hours');
        $registerSessionCommand = new RegisterSessionCommand(
            self::SESSION_ID,
            $expiryDate->format(DATE_RFC3339),
            self::READY_SESSION_ID
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
        $registerSessionCommand = new RegisterSessionCommand(
            self::SESSION_ID,
            $expiryDate->format(DATE_RFC3339),
            self::READY_SESSION_ID
        );

        static::$registerSessionCommandHandler->__invoke($registerSessionCommand);

        $item = self::$cacheItemPool->getItem('ready_session_id_' . self::READY_SESSION_ID);

        $this->assertFalse($item->isHit());
    }

    private function getFormattedJsonResponse(DateTimeImmutable $expiryDate): string
    {
        return sprintf(
            '{"id":"%s","expiryDate":%d,"readySessionId":"%s"}',
            self::SESSION_ID,
            $expiryDate->getTimestamp(),
            self::READY_SESSION_ID
        );
    }
}
