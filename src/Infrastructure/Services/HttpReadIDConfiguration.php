<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use function rtrim;

final class HttpReadIDConfiguration
{
    private const AUTHORIZATION_TOKEN = 'app.authorization_token';

    private const OPAQUE_ID = 'app.opaque_id';

    private const READID_ENDPOINT = 'app.readid_endpoint';

    /** @var ContainerBagInterface */
    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function authorizationToken(): string
    {
        return $this->params->get(self::AUTHORIZATION_TOKEN);
    }

    public function opaqueId(): string
    {
        return $this->params->get(self::OPAQUE_ID);
    }

    public function endpoint(): string
    {
        return rtrim($this->params->get(self::READID_ENDPOINT), '/');
    }
}
