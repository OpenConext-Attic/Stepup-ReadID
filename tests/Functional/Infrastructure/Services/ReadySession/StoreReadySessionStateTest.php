<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Functional\Infrastructure\Services\ReadySession;

use StepupReadId\Domain\ReadySession\Exception\ReadySessionNotFoundException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Domain\ReadySession\Services\ReadySessionStateInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StoreReadySessionStateTest extends KernelTestCase
{
    public const READY_SESSION_ID          = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const READY_SESSION_BASE64IMAGE = 'data:image/png;base64,0';
    public const READY_SESSION_JWT_TOKEN   = 'j.w.t';
    public const READY_SESSION_TIMESTAMP   = 1;
    /** @var ReadySessionStateInterface */
    private $storeReadySessionState;

    protected function setUp(): void
    {
        self::bootKernel();

        $container                    = self::$kernel->getContainer();
        $this->storeReadySessionState = $container->get('test.' . ReadySessionStateInterface::class);
    }

    public function testStoreIsEmptyByDefault(): void
    {
        $this->expectException(ReadySessionNotFoundException::class);

        $this->storeReadySessionState->load();
    }

    public function testStoreReadySessionInstance(): void
    {
        $readySession = ReadySession::create(
            ReadySessionId::fromString(self::READY_SESSION_ID),
            ReadySessionBase64Image::fromString(self::READY_SESSION_BASE64IMAGE),
            ReadySessionJwtToken::fromString(self::READY_SESSION_JWT_TOKEN),
            ReadySessionTimestamp::fromInteger(self::READY_SESSION_TIMESTAMP)
        );

        $this->storeReadySessionState->save($readySession);

        $this->assertEquals(
            $readySession,
            $this->storeReadySessionState->load()
        );
    }
}
