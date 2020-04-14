<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Serializer;

use StepupReadId\Domain\Session\Model\Session;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SessionNormalizer implements NormalizerInterface
{
    /**
     * @param Session $object
     *
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->id()->value(),
            'expiryDate' => $object->expiryDate()->value()->getTimestamp(),
            'readySessionId' => $object->readySessionId()->value(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Session;
    }
}
