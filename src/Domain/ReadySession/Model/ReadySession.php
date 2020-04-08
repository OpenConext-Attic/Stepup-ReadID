<?php

declare(strict_types=1);

namespace StepupReadId\Domain\ReadySession\Model;

final class ReadySession
{
    /** @var ReadySessionId  */
    private $id;
    /** @var ReadySessionBase64Image  */
    private $qrCode;
    /** @var ReadySessionJwtToken  */
    private $jwtToken;
    /** @var ReadySessionTimestamp  */
    private $timestamp;

    private function __construct(
        ReadySessionId $id,
        ReadySessionBase64Image $qrCode,
        ReadySessionJwtToken $jwtToken,
        ReadySessionTimestamp $timestamp
    ) {
        $this->id        = $id;
        $this->qrCode    = $qrCode;
        $this->jwtToken  = $jwtToken;
        $this->timestamp = $timestamp;
    }

    public static function create(
        ReadySessionId $id,
        ReadySessionBase64Image $qrCode,
        ReadySessionJwtToken $jwtToken,
        ReadySessionTimestamp $timestamp
    ): ReadySession {
        return new self($id, $qrCode, $jwtToken, $timestamp);
    }

    public function id(): ReadySessionId
    {
        return $this->id;
    }

    public function qrCode(): ReadySessionBase64Image
    {
        return $this->qrCode;
    }

    public function jwtToken(): ReadySessionJwtToken
    {
        return $this->jwtToken;
    }

    public function timestamp(): ReadySessionTimestamp
    {
        return $this->timestamp;
    }
}
