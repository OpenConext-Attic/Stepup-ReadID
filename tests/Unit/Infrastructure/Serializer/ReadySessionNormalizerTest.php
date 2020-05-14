<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use stdClass;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Infrastructure\Serializer\ReadySessionNormalizer;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ReadySessionNormalizerTest extends TestCase
{
    public const READY_SESSION_ID          = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const READY_SESSION_BASE64IMAGE = 'data:image/png;base64,0';
    public const READY_SESSION_JWT_TOKEN   = 'j.w.t';
    public const READY_SESSION_TIMESTAMP   = 1;
    /** @var ReadySessionNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ReadySessionNormalizer();
    }

    public function testReadySessionNormalization(): void
    {
        $readySession = ReadySession::create(
            ReadySessionId::fromString(self::READY_SESSION_ID),
            ReadySessionBase64Image::fromString(self::READY_SESSION_BASE64IMAGE),
            ReadySessionJwtToken::fromString(self::READY_SESSION_JWT_TOKEN),
            ReadySessionTimestamp::fromInteger(self::READY_SESSION_TIMESTAMP)
        );
        $this->assertTrue($this->normalizer->supportsNormalization($readySession));

        $normalizedReadySession = $this->normalizer->normalize($readySession);
        $this->assertEquals([
            ReadySessionNormalizer::READY_SESSION_ID => self::READY_SESSION_ID,
            ReadySessionNormalizer::BASE_64_QR => self::READY_SESSION_BASE64IMAGE,
            ReadySessionNormalizer::JWK_TOKEN => self::READY_SESSION_JWT_TOKEN,
            ReadySessionNormalizer::EXPIRY_TIMESTAMP => self::READY_SESSION_TIMESTAMP,
        ], $normalizedReadySession);
    }

    public function testNotSupportedNormalizationObjects(): void
    {
        $this->assertFalse($this->normalizer->supportsNormalization(new stdClass()));
    }

    public function testInvalidObjectNormalization(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->normalizer->normalize(new stdClass());
    }

    public function testReadySessionDenormalization(): void
    {
        $data = [
            ReadySessionNormalizer::READY_SESSION_ID => self::READY_SESSION_ID,
            ReadySessionNormalizer::BASE_64_QR => self::READY_SESSION_BASE64IMAGE,
            ReadySessionNormalizer::JWK_TOKEN => self::READY_SESSION_JWT_TOKEN,
            ReadySessionNormalizer::EXPIRY_TIMESTAMP => self::READY_SESSION_TIMESTAMP,
        ];
        $this->assertTrue($this->normalizer->supportsDenormalization($data, ReadySession::class));

        $readySession = $this->normalizer->denormalize($data, ReadySession::class);
        $this->assertTrue($readySession->id()->equals(
            ReadySessionId::fromString(self::READY_SESSION_ID)
        ));
        $this->assertTrue($readySession->qrCode()->equals(
            ReadySessionBase64Image::fromString(self::READY_SESSION_BASE64IMAGE)
        ));
        $this->assertTrue($readySession->jwtToken()->equals(
            ReadySessionJwtToken::fromString(self::READY_SESSION_JWT_TOKEN)
        ));
        $this->assertTrue($readySession->timestamp()->equals(
            ReadySessionTimestamp::fromInteger(self::READY_SESSION_TIMESTAMP)
        ));
    }

    public function testNotSupportedDenormalizationObjects(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization(null, ReadySession::class));
        $this->assertFalse($this->normalizer->supportsDenormalization([], stdClass::class));
    }

    public function testInvalidEmptyDataDenormalization(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->normalizer->denormalize(null, stdClass::class);
    }

    public function testInvalidDataDenormalization(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->normalizer->denormalize([], ReadySession::class);
    }

    public function testInvalidObjectDenormalization(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->normalizer->denormalize([], stdClass::class);
    }
}
