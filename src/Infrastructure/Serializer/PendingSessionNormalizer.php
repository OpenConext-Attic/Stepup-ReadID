<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Serializer;

use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Model\PendingSessionExpiryDate;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\Session\Model\SessionId;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function is_array;

final class PendingSessionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!$object instanceof PendingSession) {
            throw new InvalidArgumentException();
        }

        return [
            'readySessionId' => $object->readySessionId()->value(),
            'expiryDate' => $object->expiryDate()->value()->getTimestamp(),
            'sessionId' => $object->sessionId() ? $object->sessionId()->value() : null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PendingSession;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (is_array($data) === false) {
            throw new NotNormalizableValueException();
        }

        if ($type !== PendingSession::class) {
            throw new InvalidArgumentException();
        }

        try {
            if ($data['sessionId']) {
                return PendingSession::createWithSession(
                    ReadySessionId::fromString($data['readySessionId']),
                    PendingSessionExpiryDate::fromTimestamp($data['expiryDate']),
                    SessionId::fromString($data['sessionId'])
                );
            }

            return PendingSession::create(
                ReadySessionId::fromString($data['readySessionId']),
                PendingSessionExpiryDate::fromTimestamp($data['expiryDate'])
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
        return is_array($data) && $type === PendingSession::class;
    }
}
