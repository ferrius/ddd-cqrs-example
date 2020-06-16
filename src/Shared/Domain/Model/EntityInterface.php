<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\DDD;

interface EntityInterface
{
    public function getId(): int;
}
