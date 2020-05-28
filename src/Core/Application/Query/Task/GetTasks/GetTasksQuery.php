<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Task\GetTasks;

use App\Shared\Infrastructure\ValueObject\Pagination;

final class GetTasksQuery
{
    private Pagination $pagination;

    private ?\DateTimeImmutable $executionDate;

    private ?string $searchText;

    public function __construct(Pagination $pagination, ?\DateTimeImmutable $executionDate = null, ?string $searchText = null)
    {
        $this->pagination = $pagination;
        $this->executionDate = $executionDate;
        $this->searchText = $searchText;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function getExecutionDate(): ?\DateTimeImmutable
    {
        return $this->executionDate;
    }

    public function getSearchText(): ?string
    {
        return $this->searchText;
    }
}
