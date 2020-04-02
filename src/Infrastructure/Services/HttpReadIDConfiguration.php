<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

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
}
