<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

use App\Shared\Domain\DomainEventInterface;

final class TaskCreatedEvent implements DomainEventInterface
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
