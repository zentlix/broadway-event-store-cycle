<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Entity;

interface FactoryInterface
{
    /**
     * @psalm-param positive-int|0 $playhead
     * @psalm-param non-empty-string $payload
     * @psalm-param non-empty-string $metadata
     * @psalm-param non-empty-string $type
     */
    public function create(
        mixed $uuid,
        int $playhead,
        string $payload,
        string $metadata,
        string $type,
        \DateTimeInterface $recordedOn = new \DateTimeImmutable()
    ): EventInterface;
}
