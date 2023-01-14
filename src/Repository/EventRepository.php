<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle\Repository;

use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\Select\Repository;

class EventRepository extends Repository implements EventRepositoryInterface
{
    public function load(mixed $id): iterable
    {
        return $this->findAll(['uuid' => $id], ['playhead' => SelectQuery::SORT_ASC]);
    }

    public function loadFromPlayhead(mixed $id, int $playhead): iterable
    {
        return $this
            ->select()
            ->where('uuid', $id)
            ->andWhere('playhead', '>=', $playhead)
            ->orderBy('playhead', SelectQuery::SORT_ASC)
            ->fetchAll();
    }
}
