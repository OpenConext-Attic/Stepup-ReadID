<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionAuthorizationException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionBadRequestException;
use StepupReadId\Domain\ReadySession\Exception\RequestReadySessionConnectionException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Model\ReadySessionBase64Image;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use StepupReadId\Domain\ReadySession\Model\ReadySessionJwtToken;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTimestamp;
use StepupReadId\Domain\ReadySession\Model\ReadySessionTTL;
use StepupReadId\Domain\ReadySession\Services\RequestReadySessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function array_diff_key;
use function array_flip;
use function intval;

final class HttpRequestReadySession implements RequestReadySessionInterface
{
    public const READY_SESSION_ID = 'readySessionID';
    public const BASE_64_QR       = 'base64QR';
    public const JWK_TOKEN        = 'jwtToken';
    public const EXPIRY_TIMESTAMP = 'expiryTimestamp';

    /** @var HttpReadIDClientInterface */
    private $httpReadIDClient;

    public function __construct(HttpReadIDClientInterface $httpReadIDClient)
    {
        $this->httpReadIDClient = $httpReadIDClient;
    }

    public function with(ReadySessionTTL $ttl): ReadySession
    {
        $response = $this->httpReadIDClient->createReadySession($ttl->value());

        try {
            if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                throw new RequestReadySessionAuthorizationException();
            }

            if ($response->getStatusCode() !== Response::HTTP_CREATED) {
                throw new RequestReadySessionBadRequestException();
            }

            $decodedPayload = $response->toArray();
            $this->checkPayload($decodedPayload);
        } catch (TransportExceptionInterface $e) {
            throw RequestReadySessionConnectionException::becauseTransportError($e->getMessage());
        }

        return ReadySession::create(
            ReadySessionId::fromString($decodedPayload[self::READY_SESSION_ID]),
            ReadySessionBase64Image::fromString($decodedPayload[self::BASE_64_QR]),
            ReadySessionJwtToken::fromString($decodedPayload[self::JWK_TOKEN]),
            ReadySessionTimestamp::fromInteger(intval($decodedPayload[self::EXPIRY_TIMESTAMP]))
        );
    }

    /**
     * @param array<string,string> $decodedPayload
     */
    private function checkPayload(array $decodedPayload): void
    {
        if (array_diff_key(
            array_flip([
                self::READY_SESSION_ID,
                self::BASE_64_QR,
                self::JWK_TOKEN,
                self::EXPIRY_TIMESTAMP,
            ]),
            $decodedPayload
        )) {
            throw RequestReadySessionConnectionException::becauseResponseIsInvalid();
        }
    }
}
