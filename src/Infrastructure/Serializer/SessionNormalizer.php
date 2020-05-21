<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Serializer;

use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionDocument;
use StepupReadId\Domain\Session\Model\SessionDocumentAttribute;
use StepupReadId\Domain\Session\Model\SessionExpiryDate;
use StepupReadId\Domain\Session\Model\SessionId;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use function is_array;
use function is_string;

final class SessionNormalizer implements DenormalizerInterface
{
    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (is_array($data) === false) {
            throw new NotNormalizableValueException();
        }

        if ($type !== Session::class) {
            throw new InvalidArgumentException();
        }

        try {
            $sessionDocument = $this->processDocumentContent($data['documentContent']);

            return Session::create(
                SessionId::fromString($data['sessionId']),
                SessionExpiryDate::fromISOString($data['expiryDate']),
                $sessionDocument,
                ReadySessionId::fromString($data['readySession']['readySessionId'])
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
        return is_array($data) && $type === Session::class;
    }

    /**
     * @param array<string,mixed> $documentContent
     */
    private function processDocumentContent(array $documentContent): SessionDocument
    {
        $document = SessionDocument::empty();

        foreach ($documentContent as $key => $data) {
            if (!is_string($data)) {
                continue;
            }

            $document->add(SessionDocumentAttribute::with($key, $data));
        }

        return $document;
    }
}
