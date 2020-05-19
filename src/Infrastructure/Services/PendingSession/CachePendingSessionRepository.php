<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\PendingSession;

use Psr\Cache\CacheItemPoolInterface;
use StepupReadId\Domain\PendingSession\Exception\PendingSessionNotFoundException;
use StepupReadId\Domain\PendingSession\Model\PendingSession;
use StepupReadId\Domain\PendingSession\Services\PendingSessionRepositoryInterface;
use StepupReadId\Domain\ReadySession\Model\ReadySessionId;
use Symfony\Component\Serializer\SerializerInterface;

final class CachePendingSessionRepository implements PendingSessionRepositoryInterface
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

    public function save(PendingSession $pendingSession): void
    {
        $item = $this->cacheItemPool->getItem('ready_session_id_' . $pendingSession->readySessionId()->value());

        $item->set($this->serializer->serialize($pendingSession, 'json'));
        $item->expiresAt($pendingSession->expiryDate()->value());

        $this->cacheItemPool->save($item);
    }

    public function findOneByReadySession(ReadySessionId $readySessionId): PendingSession
    {
        $item = $this->cacheItemPool->getItem('ready_session_id_' . $readySessionId->value());

        if (! $item->isHit()) {
            throw PendingSessionNotFoundException::withReadySessionId($readySessionId);
        }

        return $this->serializer->deserialize($item->get(), PendingSession::class, 'json');
    }
}
