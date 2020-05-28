<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\CreateTask;

use App\Core\Domain\Model\Task\Task;
use App\Core\Infrastructure\Repository\TaskRepository;
use App\Core\Infrastructure\Security\UserFetcherInterface;

final class CreateTaskCommandHandler
{
    private TaskRepository $taskRepository;

    private UserFetcherInterface $userFetcher;

    public function __construct(TaskRepository $taskRepository, UserFetcherInterface $userFetcher)
    {
        $this->taskRepository = $taskRepository;
        $this->userFetcher = $userFetcher;
    }

    public function __invoke(CreateTaskCommand $command): int
    {
        $user = $this->userFetcher->fetchRequiredUser();

        $task = new Task($command->getTitle(), $command->getExecutionDay(), $user, $command->getDescription());
        $this->taskRepository->add($task);

        return $task->getId();
    }
}
