<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadySession;

use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionConnectionException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionOpaqueId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Domain\ReadySession\Services\RequestReadySessionInterface;
use StepupReadId\Infrastructure\Services\ReadId\ReadIdClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class HttpRequestReadySession implements RequestReadySessionInterface
{
    public const CREATE_READY_SESSION_ENDPOINT_PATH = '/odata/v1/ODataServlet/createReadySession';

    /** @var ReadIdClientInterface */
    private $httpSubmitterClient;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ReadIdClientInterface $httpSubmitterClient, SerializerInterface $serializer)
    {
        $this->httpSubmitterClient = $httpSubmitterClient;
        $this->serializer          = $serializer;
    }

    public function with(ReadySessionOpaqueId $opaqueId, ReadySessionTTL $qrCodeTtl): ReadySession
    {
        $response = $this->httpSubmitterClient->post(
            self::CREATE_READY_SESSION_ENDPOINT_PATH,
            [
                'opaqueID' => $opaqueId->value(),
                'TTL' => $qrCodeTtl->value(),
            ]
        );

        try {
            if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                throw RequestReadySessionConnectionException::becauseUnauthorized();
            }

            if ($response->getStatusCode() !== Response::HTTP_CREATED) {
                throw RequestReadySessionConnectionException::becauseBadRequest($response->getStatusCode());
            }

            $payload = $response->getContent();
        } catch (TransportExceptionInterface $e) {
            throw RequestReadySessionConnectionException::becauseTransportError($e->getMessage());
        }

        try {
            $readySession = $this->serializer->deserialize($payload, ReadySession::class, 'json');
        } catch (NotNormalizableValueException $e) {
            throw RequestReadySessionConnectionException::becauseResponseIsInvalid();
        }

        return $readySession;
    }
}
