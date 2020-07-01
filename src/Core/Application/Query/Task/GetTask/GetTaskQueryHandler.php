<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Task\GetTask;

use App\Core\Application\Query\Task\DTO\TaskDTO;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\User\UserFetcherInterface;
use App\Shared\Domain\Exception\AccessForbiddenException;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class GetTaskQueryHandler
{
    private EntityManagerInterface $em;

    private UserFetcherInterface $userFetcher;

    public function __construct(EntityManagerInterface $em, UserFetcherInterface $userFetcher)
    {
        $this->em = $em;
        $this->userFetcher = $userFetcher;
    }

    public function __invoke(GetTaskQuery $query): TaskDTO
    {
        $task = $this->em->find(Task::class, $query->getId());

        if ($task === null) {
            throw new ResourceNotFoundException(sprintf('Task with id "%s" is not found', $query->getId()));
        }

        $user = $this->userFetcher->fetchRequiredUser();

        if (!$task->getUser()->equals($user)) {
            throw new AccessForbiddenException('Access prohibited');
        }

        return TaskDTO::fromEntity($task);
    }
}
