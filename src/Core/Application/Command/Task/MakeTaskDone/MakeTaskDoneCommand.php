<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\MakeTaskDone;

final class MakeTaskDoneCommand
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
