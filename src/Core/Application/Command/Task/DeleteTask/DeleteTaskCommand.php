<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\DeleteTask;

final class DeleteTaskCommand
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
