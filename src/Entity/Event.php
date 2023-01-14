<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Entity;

class Event implements EventInterface
{
    public function __construct(
        protected string $uuid,
        protected int $playhead,
        protected string $payload,
        protected string $metadata,
        protected string $type,
        protected \DateTimeInterface $recordedOn = new \DateTimeImmutable()
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->uuid;
    }

    /**
     * @psalm-return positive-int|0
     */
    public function getPlayhead(): int
    {
        return $this->playhead;
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getMetadata(): string
    {
        return $this->metadata;
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getRecordedOn(): \DateTimeInterface
    {
        return $this->recordedOn;
    }
}
