<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Task\DTO;

use App\Core\Domain\Model\Task\Task;

final class TaskDTO
{
    private int $id;

    private string $title;

    private string $description;

    private string $status;

    private \DateTimeImmutable $executionDay;

    private \DateTimeImmutable $createdAt;

    public static function fromEntity(Task $task): TaskDTO
    {
        $dto = new static();
        $dto->setId($task->getId());
        $dto->setTitle($task->getTitle());
        $dto->setDescription($task->getDescription());
        $dto->setStatus((string) $task->getStatus());
        $dto->setExecutionDay($task->getExecutionDay());
        $dto->setCreatedAt($task->getCreatedAt());

        return $dto;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return TaskDTO
     */
    public static function fromQueryArray(array $data): TaskDTO
    {
        if (!isset($data['id'], $data['title'], $data['description'], $data['status'], $data['execution_day'], $data['created_at'])) {
            throw new \InvalidArgumentException(sprintf('Not all keys are set or null %s', var_export($data, true)));
        }

        $dto = new static();
        $dto->setId((int) $data['id']);
        $dto->setTitle($data['title']);
        $dto->setDescription($data['description']);
        $dto->setStatus($data['status']);
        $dto->setExecutionDay(new \DateTimeImmutable($data['execution_day']));
        $dto->setCreatedAt(new \DateTimeImmutable($data['created_at']));

        return $dto;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getExecutionDay(): \DateTimeImmutable
    {
        return $this->executionDay;
    }

    public function setExecutionDay(\DateTimeImmutable $executionDay): void
    {
        $this->executionDay = $executionDay;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
