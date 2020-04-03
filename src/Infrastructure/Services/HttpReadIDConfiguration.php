<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

use InvalidArgumentException;
use function filter_var;
use const FILTER_FLAG_HOSTNAME;
use const FILTER_VALIDATE_DOMAIN;

final class HttpReadIDConfiguration
{
    /** @var string */
    private $authorizationToken;
    /** @var string */
    private $opaqueId;
    /** @var string */
    private $readIdServerFqdn;

    public function __construct(string $authorizationToken, string $opaqueId, string $readIdServerFqdn)
    {
        $this->checkValidAuthorizationToken($authorizationToken);
        $this->checkValidOpaqueId($opaqueId);
        $this->checkValidHostName($readIdServerFqdn);

        $this->authorizationToken = $authorizationToken;
        $this->opaqueId           = $opaqueId;
        $this->readIdServerFqdn   = $readIdServerFqdn;
    }

    public function authorizationToken(): string
    {
        return $this->authorizationToken;
    }

    public function opaqueId(): string
    {
        return $this->opaqueId;
    }

    public function readIdServerFqdn(): string
    {
        return $this->readIdServerFqdn;
    }

    private function checkValidAuthorizationToken(string $authorizationToken): void
    {
        if (empty($authorizationToken)) {
            throw new InvalidArgumentException('Authorization token can not be empty');
        }
    }

    private function checkValidOpaqueId(string $opaqueId): void
    {
        if (empty($opaqueId)) {
            throw new InvalidArgumentException('Opaque ID can not be empty');
        }
    }

    private function checkValidHostName(string $readIdServerFqdn): void
    {
        if (!filter_var($readIdServerFqdn, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidArgumentException("Invalid hostname: '$readIdServerFqdn'");
        }
    }
}
