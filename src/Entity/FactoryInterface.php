<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Entity;

interface FactoryInterface
{
    public function create(
        mixed $uuid,
        int $playhead,
        string $payload,
        string $metadata,
        string $type,
        \DateTimeInterface $recordedOn = new \DateTimeImmutable()
    ): EventInterface;
}
