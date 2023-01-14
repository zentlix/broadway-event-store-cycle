<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Repository;

use Spiral\Broadway\EventStore\Cycle\Entity\EventInterface;

interface EventRepositoryInterface
{
    /**
     * @psalm-return EventInterface[]
     */
    public function load(mixed $id): iterable;

    /**
     * @psalm-return EventInterface[]
     */
    public function loadFromPlayhead(mixed $id, int $playhead): iterable;

    /**
     * @psalm-return EventInterface[]
     */
    public function findAll(array $scope = [], array $orderBy = []): iterable;
}
