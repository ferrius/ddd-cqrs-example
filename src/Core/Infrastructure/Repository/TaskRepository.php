<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskRepository implements TaskRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?Task
    {
        return $this->em->find(Task::class, $id);
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }
}
