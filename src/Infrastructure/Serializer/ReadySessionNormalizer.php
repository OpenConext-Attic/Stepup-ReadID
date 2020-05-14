<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Serializer;

use RuntimeException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function is_array;

final class ReadySessionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public const READY_SESSION_ID = 'readySessionID';
    public const BASE_64_QR       = 'base64QR';
    public const JWK_TOKEN        = 'jwtToken';
    public const EXPIRY_TIMESTAMP = 'expiryTimestamp';

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!$object instanceof ReadySession) {
            throw new InvalidArgumentException();
        }

        return [
            self::READY_SESSION_ID => $object->id()->value(),
            self::BASE_64_QR => $object->qrCode()->value(),
            self::JWK_TOKEN => $object->jwtToken()->value(),
            self::EXPIRY_TIMESTAMP => $object->timestamp()->value(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ReadySession;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (is_array($data) === false) {
            throw new NotNormalizableValueException();
        }

        if ($type !== ReadySession::class) {
            throw new InvalidArgumentException();
        }

        try {
            return ReadySession::create(
                ReadySessionId::fromString($data[self::READY_SESSION_ID]),
                ReadySessionBase64Image::fromString($data[self::BASE_64_QR]),
                ReadySessionJwtToken::fromString($data[self::JWK_TOKEN]),
                ReadySessionTimestamp::fromInteger($data[self::EXPIRY_TIMESTAMP])
            );
        } catch (RuntimeException $exception) {
            throw new NotNormalizableValueException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_array($data) && $type === ReadySession::class;
    }
}
