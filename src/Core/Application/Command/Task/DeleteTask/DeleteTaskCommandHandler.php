<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Task\DeleteTask;

use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Infrastructure\Security\UserFetcherInterface;
use App\Shared\Infrastructure\Exception\AccessForbiddenException;
use App\Shared\Infrastructure\Exception\ResourceNotFoundException;

final class DeleteTaskCommandHandler
{
    private TaskRepositoryInterface $taskRepository;

    private UserFetcherInterface $userFetcher;

    public function __construct(TaskRepositoryInterface $taskRepository, UserFetcherInterface $userFetcher)
    {
        $this->taskRepository = $taskRepository;
        $this->userFetcher = $userFetcher;
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->getId());

        if ($task === null) {
            throw new ResourceNotFoundException(sprintf('Task with id "%s" is not found', $command->getId()));
        }

        $user = $this->userFetcher->fetchRequiredUser();

        if (!$task->getUser()->equals($user)) {
            throw new AccessForbiddenException('Access prohibited');
        }

        $this->taskRepository->remove($task);
    }
}
