<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\CreateTask;

use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Domain\Model\User\UserFetcherInterface;

final class CreateTaskCommandHandler
{
    private TaskRepositoryInterface $taskRepository;

    private UserFetcherInterface $userFetcher;

    public function __construct(TaskRepositoryInterface $taskRepository, UserFetcherInterface $userFetcher)
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
