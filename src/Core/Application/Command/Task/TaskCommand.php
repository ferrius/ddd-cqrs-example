<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task;

abstract class TaskCommand
{
    protected string $title;

    protected \DateTimeImmutable $executionDay;

    protected string $description;

    public function __construct(string $title, \DateTimeImmutable $executionDay, string $description = '')
    {
        $this->title = $title;
        $this->executionDay = $executionDay;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExecutionDay(): \DateTimeImmutable
    {
        return $this->executionDay;
    }
}
