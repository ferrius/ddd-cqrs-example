<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ValueObject;

final class PaginatedData
{
    /**
     * @var array<int, mixed>
     */
    private array $data;

    private int $count;

    /**
     * @param array<int, mixed> $data
     * @param int $count
     */
    public function __construct(array $data, int $count)
    {
        $this->data = $data;
        $this->count = $count;
    }

    /**
     * @return array<int, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
