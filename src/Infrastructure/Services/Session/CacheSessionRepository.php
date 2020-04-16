<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\Session;

use Psr\Cache\CacheItemPoolInterface;
use StepupReadId\Domain\Session\Model\Session;
use StepupReadId\Domain\Session\Services\SessionRepositoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class CacheSessionRepository implements SessionRepositoryInterface
{
    /** @var CacheItemPoolInterface */
    private $cacheItemPool;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(CacheItemPoolInterface $cacheItemPool, SerializerInterface $serializer)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->serializer    = $serializer;
    }

    public function save(Session $session): void
    {
        $item = $this->cacheItemPool->getItem('ready_session_id_' . $session->readySessionId()->value());

        $item->set($this->serializer->serialize($session, 'json'));
        $item->expiresAt($session->expiryDate()->value());

        $this->cacheItemPool->save($item);
    }
}
