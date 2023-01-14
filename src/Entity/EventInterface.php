<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Entity;

interface EventInterface
{
    public function getIdentifier(): string;

    /**
     * @psalm-return positive-int|0
     */
    public function getPlayhead(): int;

    /**
     * @psalm-return non-empty-string
     */
    public function getPayload(): string;

    /**
     * @psalm-return non-empty-string
     */
    public function getMetadata(): string;

    /**
     * @psalm-return non-empty-string
     */
    public function getType(): string;

    public function getRecordedOn(): \DateTimeInterface;
}
