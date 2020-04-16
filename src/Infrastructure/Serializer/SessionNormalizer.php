<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Serializer;

use StepupReadId\Domain\Session\Model\Session;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SessionNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!$object instanceof Session) {
            throw new InvalidArgumentException();
        }

        return [
            'id' => $object->id()->value(),
            'expiryDate' => $object->expiryDate()->value()->getTimestamp(),
            'readySessionId' => $object->readySessionId()->value(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Session;
    }
}
