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
    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!$object instanceof ReadySession) {
            throw new InvalidArgumentException();
        }

        return [
            'id' => $object->id()->value(),
            'qrCode' => $object->qrCode()->value(),
            'jwtToken' => $object->jwtToken()->value(),
            'timestamp' => $object->timestamp()->value(),
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
                ReadySessionId::fromString($data['id']),
                ReadySessionBase64Image::fromString($data['qrCode']),
                ReadySessionJwtToken::fromString($data['jwtToken']),
                ReadySessionTimestamp::fromInteger($data['timestamp'])
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
