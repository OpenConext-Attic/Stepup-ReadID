<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\Session;

use StepupReadId\Domain\Session\Exception\RequestSessionConnectionException;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Model\SessionId;
use StepupReadId\Domain\Session\Services\RequestSessionInterface;
use StepupReadId\Infrastructure\Services\ReadId\ReadIdClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class HttpRequestSession implements RequestSessionInterface
{
    public const GET_SESSION_ENDPOINT_PATH = '/odata/v1/ODataServlet/Sessions(\'%s\')';

    /** @var ReadIdClientInterface */
    private $viewerClient;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ReadIdClientInterface $viewerClient, SerializerInterface $serializer)
    {
        $this->viewerClient = $viewerClient;
        $this->serializer   = $serializer;
    }

    public function with(SessionId $sessionId): Session
    {
        $response = $this->viewerClient->get(
            self::GET_SESSION_ENDPOINT_PATH,
            [$sessionId->value()]
        );

        try {
            if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                throw RequestSessionConnectionException::becauseUnauthorized();
            }

            if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
                throw RequestSessionConnectionException::becauseNotFound($sessionId);
            }

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw RequestSessionConnectionException::becauseBadRequest($response->getStatusCode());
            }

            $decodedPayload = $response->getContent();
        } catch (TransportExceptionInterface $e) {
            throw RequestSessionConnectionException::becauseTransportError($e->getMessage());
        }

        try {
            $session = $this->serializer->deserialize($decodedPayload, Session::class, 'json');
        } catch (NotNormalizableValueException $e) {
            throw RequestSessionConnectionException::becauseResponseIsInvalid();
        }

        return $session;
    }
}
