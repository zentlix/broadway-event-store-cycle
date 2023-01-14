<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Repository;

interface EventRepositoryInterface
{
    public function load(mixed $id): iterable;

    public function loadFromPlayhead(mixed $id, int $playhead): iterable;

    public function findAll(array $scope = [], array $orderBy = []): iterable;
}
