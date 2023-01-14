<?php

declare(strict_types=1);

namespace Spiral\Broadway\EventStore\Cycle;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventStore\EventStore;
use Broadway\EventStore\EventStreamNotFoundException;
use Broadway\EventStore\EventVisitor;
use Broadway\EventStore\Management\Criteria;
use Broadway\EventStore\Management\CriteriaNotSupportedException;
use Broadway\EventStore\Management\EventStoreManagement;
use Broadway\Serializer\Serializer;
use Cycle\Database\Injection\Parameter;
use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\EntityManagerInterface;
use Spiral\Broadway\EventStore\Cycle\Entity\EventInterface;
use Spiral\Broadway\EventStore\Cycle\Entity\FactoryInterface;
use Spiral\Broadway\EventStore\Cycle\Repository\EventRepositoryInterface;

class CycleEventStore implements EventStore, EventStoreManagement
{
    public function __construct(
        protected readonly EventRepositoryInterface $eventRepository,
        protected readonly Serializer $payloadSerializer,
        protected readonly Serializer $metadataSerializer,
        protected readonly FactoryInterface $factory,
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function load(mixed $id): DomainEventStream
    {
        $events = [];
        foreach ($this->eventRepository->load((string) $id) as $event) {
            $events[] = $this->deserializeEvent($event);
        }

        if (empty($events)) {
            throw new EventStreamNotFoundException(
                sprintf('EventStream not found for aggregate with id `%s`.', $id)
            );
        }

        return new DomainEventStream($events);
    }

    /**
     * @throws \JsonException
     */
    public function loadFromPlayhead(mixed $id, int $playhead): DomainEventStream
    {
        $result = $this->eventRepository->loadFromPlayhead((string) $id, $playhead);

        $events = [];
        foreach ($result as $event) {
            $events[] = $this->deserializeEvent($event);
        }

        return new DomainEventStream($events);
    }

    /**
     * @throws \JsonException
     */
    public function append(mixed $id, DomainEventStream $eventStream): void
    {
        /** @var DomainMessage $domainMessage */
        foreach ($eventStream as $domainMessage) {
            $this->entityManager->persist($this->createEvent($domainMessage));
        }

        $this->entityManager->run();
    }

    /**
     * @throws \JsonException
     */
    public function visitEvents(Criteria $criteria, EventVisitor $eventVisitor): void
    {
        foreach ($this->getEventsByCriteria($criteria) as $event) {
            $domainMessage = $this->deserializeEvent($event);

            $eventVisitor->doWithEvent($domainMessage);
        }
    }

    /**
     * @throws \JsonException
     */
    private function deserializeEvent(EventInterface $event): DomainMessage
    {
        $payload = $this->payloadSerializer->deserialize(
            json_decode($event->getPayload(), true, 512, \JSON_THROW_ON_ERROR)
        );
        $metadata = $this->metadataSerializer->deserialize(
            json_decode($event->getMetadata(), true, 512, \JSON_THROW_ON_ERROR)
        );

        return new DomainMessage(
            $event->getIdentifier(),
            $event->getPlayhead(),
            $metadata,
            $payload,
            DateTime::fromString($event->getRecordedOn()->format(DateTime::FORMAT_STRING))
        );
    }

    /**
     * @throws \JsonException
     */
    private function createEvent(DomainMessage $domainMessage): EventInterface
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return $this->factory->create(
            $domainMessage->getId(),
            $domainMessage->getPlayhead(),
            json_encode($this->payloadSerializer->serialize($domainMessage->getPayload()), \JSON_THROW_ON_ERROR),
            json_encode($this->metadataSerializer->serialize($domainMessage->getMetadata()), \JSON_THROW_ON_ERROR),
            $domainMessage->getType(),
            $domainMessage->getRecordedOn()->toNative()
        );
    }

    /**
     * @return EventInterface[]
     */
    private function getEventsByCriteria(Criteria $criteria): iterable
    {
        if ($criteria->getAggregateRootTypes()) {
            throw new CriteriaNotSupportedException(
                'Cycle implementation cannot support criteria based on aggregate root types.'
            );
        }

        if ($criteria->getAggregateRootIds()) {
            return $this->eventRepository->findAll(
                ['uuid' => ['in' => new Parameter($criteria->getAggregateRootIds())]],
                ['recorderOn' => SelectQuery::SORT_ASC]
            );
        }

        if ($criteria->getEventTypes()) {
            return $this->eventRepository->findAll(
                ['type' => ['in' => new Parameter($criteria->getEventTypes())]],
                ['recorderOn' => SelectQuery::SORT_ASC]
            );
        }

        return [];
    }
}
