<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Entity;

final class Factory implements FactoryInterface
{
    public function create(
        mixed $uuid,
        int $playhead,
        string $payload,
        string $metadata,
        string $type,
        \DateTimeInterface $recordedOn = new \DateTimeImmutable()
    ): EventInterface {
        return new Event($uuid, $playhead, $payload, $metadata, $type, $recordedOn);
    }
}
