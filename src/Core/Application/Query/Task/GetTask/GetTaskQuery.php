<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Task\GetTask;

final class GetTaskQuery
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
