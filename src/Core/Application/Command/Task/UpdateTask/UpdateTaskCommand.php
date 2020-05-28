<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\UpdateTask;

use App\Core\Application\Command\Task\TaskCommand;

final class UpdateTaskCommand extends TaskCommand
{
    private int $id;

    public function __construct(int $id, string $title, \DateTimeImmutable $executionDay, string $description = '')
    {
        parent::__construct($title, $executionDay, $description);
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
