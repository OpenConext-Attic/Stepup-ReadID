<?php

declare(strict_types=1);

namespace StepupReadId\Tests\Unit\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use stdClass;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionExpiryDate;
use StepupReadId\Infrastructure\Serializer\SessionNormalizer;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class SessionNormalizerTest extends TestCase
{
    public const SESSION_ID       = 'ce14bb4b-ea8d-4091-ba6b-5fa1e243f617';
    public const READY_SESSION_ID = '2c9a6de7-9e81-476d-aff4-35313769ef65';
    public const EXPIRY_DATE      = '2000-01-01T00:00:00.00Z';

    /** @var SessionNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new SessionNormalizer();
    }

    public function testSupportsSessionDenormalization(): void
    {
        $this->assertTrue($this->normalizer->supportsDenormalization([], Session::class));
    }

    public function testNotSupportsOtherObjects(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization([], stdClass::class));
    }

    public function testDenormalizeSession(): void
    {
        $data = [
            'sessionId' => self::SESSION_ID,
            'readySession' => ['readySessionId' => self::READY_SESSION_ID],
            'expiryDate' => self::EXPIRY_DATE,
            'documentContent' => ['attribute1' => 'value1', 'attribute2' => 'value2'],
        ];

        $session = $this->normalizer->denormalize($data, Session::class);

        $this->assertEquals(self::SESSION_ID, $session->id()->value());

        $this->assertEquals(self::READY_SESSION_ID, $session->readySessionId()->value());

        $expiryDate = SessionExpiryDate::fromISOString(self::EXPIRY_DATE);
        $this->assertTrue($session->expiryDate()->equals($expiryDate));

        $this->assertTrue($session->document()->has('attribute1'));
        $this->assertEquals('value1', $session->document()->get('attribute1')->value());

        $this->assertTrue($session->document()->has('attribute2'));
        $this->assertEquals('value2', $session->document()->get('attribute2')->value());
    }

    public function testInvalidDataDenormalization(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->normalizer->denormalize('{"json": true}', Session::class);
    }

    public function testInvalidObjectDenormalization(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->normalizer->denormalize([], stdClass::class);
    }

    public function testInvalidEmptyDataDenormalization(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->normalizer->denormalize(null, stdClass::class);
    }
}
