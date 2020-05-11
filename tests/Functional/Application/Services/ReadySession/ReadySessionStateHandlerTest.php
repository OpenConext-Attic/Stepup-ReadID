<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Application\Services\ReadySession;

use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Infrastructure\Services\ReadySession\ReadySessionStateHandler;
use Surfnet\GsspBundle\Exception\NotFound;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReadySessionStateHandlerTest extends KernelTestCase
{
    public const READY_SESSION_ID          = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const READY_SESSION_BASE64IMAGE = 'data:image/png;base64,0';
    public const READY_SESSION_JWT_TOKEN   = 'j.w.t';
    public const READY_SESSION_TIMESTAMP   = 1;
    /** @var ReadySessionStateHandler */
    private $readySessionStateHandler;

    protected function setUp(): void
    {
        self::bootKernel();

        $container                      = self::$kernel->getContainer();
        $this->readySessionStateHandler = $container->get('test.' . ReadySessionStateHandler::class);
    }

    public function testReadySessionStateHandlerIsEmptyByDefault(): void
    {
        $this->expectException(NotFound::class);

        $this->readySessionStateHandler->getReadySession();
    }

    public function testReadySessionStateHandlerStoreReadySessionInstance(): void
    {
        $readySession = ReadySession::create(
            ReadySessionId::fromString(self::READY_SESSION_ID),
            ReadySessionBase64Image::fromString(self::READY_SESSION_BASE64IMAGE),
            ReadySessionJwtToken::fromString(self::READY_SESSION_JWT_TOKEN),
            ReadySessionTimestamp::fromInteger(self::READY_SESSION_TIMESTAMP)
        );

        $this->readySessionStateHandler->saveReadySession($readySession);

        $this->assertEquals(
            $readySession,
            $this->readySessionStateHandler->getReadySession()
        );
    }
}
