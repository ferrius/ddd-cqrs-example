<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

abstract class Aggregate implements EntityInterface
{
    /**
     * @var DomainEventInterface[]
     */
    private array $events = [];

    abstract public function getId(): int;

    /**
     * @return DomainEventInterface[]
     */
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    protected function raise(DomainEventInterface $event): void
    {
        $this->events[] = $event;
    }
}
