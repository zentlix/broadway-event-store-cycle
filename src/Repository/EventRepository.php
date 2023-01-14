<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Repository;

use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\Select\Repository;
use Spiral\Broadway\EventStore\Cycle\Entity\EventInterface;

/**
 * @method EventInterface[] findAll(array $scope = [], array $orderBy = [])
 */
class EventRepository extends Repository implements EventRepositoryInterface
{
    /**
     * @psalm-return EventInterface[]
     */
    public function load(mixed $id): iterable
    {
        return $this->findAll(['uuid' => $id], ['playhead' => SelectQuery::SORT_ASC]);
    }

    /**
     * @psalm-return EventInterface[]
     */
    public function loadFromPlayhead(mixed $id, int $playhead): iterable
    {
        /** @var EventInterface[] $events */
        $events = $this
            ->select()
            ->where('uuid', $id)
            ->andWhere('playhead', '>=', $playhead)
            ->orderBy('playhead', SelectQuery::SORT_ASC)
            ->fetchAll();

        return $events;
    }
}
