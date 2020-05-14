<?php

declare(strict_types=1);

namespace StepupReadId\Infrastructure\Services\ReadySession;

use StepupReadId\Domain\ReadySession\Exception\ReadySessionNotFoundException;
use StepupReadId\Domain\ReadySession\Model\ReadySession;
use StepupReadId\Domain\ReadySession\Services\ReadySessionStateInterface;
use Surfnet\GsspBundle\Service\ValueStore;
use Symfony\Component\Serializer\SerializerInterface;

final class StoreReadySessionState implements ReadySessionStateInterface
{
    /** @var ValueStore */
    private $store;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ValueStore $store, SerializerInterface $serializer)
    {
        $this->store      = $store;
        $this->serializer = $serializer;
    }

    public function save(ReadySession $readySession): void
    {
        $data = $this->serializer->serialize($readySession, 'json');

        $this->store->set('readySession', $data);
    }

    public function load(): ReadySession
    {
        if (! $this->store->has('readySession')) {
            throw ReadySessionNotFoundException::stateIsEmpty();
        }

        $data = $this->store->get('readySession');

        return $this->serializer->deserialize($data, ReadySession::class, 'json');
    }
}
