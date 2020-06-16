<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

interface TaskRepositoryInterface
{
    public function find(int $id): ?Task;

    public function add(Task $task): void;

    public function remove(Task $task): void;
}
