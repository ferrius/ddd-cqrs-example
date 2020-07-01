<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Task\GetTasks;

use App\Core\Application\Query\Task\DTO\TaskDTO;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\User\UserFetcherInterface;
use App\Shared\Infrastructure\Type\DateTimeFormat;
use App\Shared\Infrastructure\ValueObject\PaginatedData;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

final class GetTasksQueryHandler
{
    private EntityManagerInterface $em;

    private UserFetcherInterface $userFetcher;

    public function __construct(EntityManagerInterface $em, UserFetcherInterface $userFetcher)
    {
        $this->em = $em;
        $this->userFetcher = $userFetcher;
    }

    public function __invoke(GetTasksQuery $query): PaginatedData
    {
        $userId = $this->userFetcher->fetchRequiredUser()->getId();

        $qb = $this->buildQuery($query, $userId);
        $tasks = $this->em->getConnection()->executeQuery($qb->getSQL(), $qb->getParameters())->fetchAll(\PDO::FETCH_ASSOC);

        $taskDTOs = [];

        foreach ($tasks as $task) {
            $taskDTOs[] = TaskDTO::fromQueryArray($task);
        }

        $qb = $this->buildQuery($query, $userId)
            ->select('COUNT(*)')
            ->setMaxResults(null)
            ->setFirstResult(0);

        $count = (int) $this->em->getConnection()->executeQuery($qb->getSQL(), $qb->getParameters())->fetchColumn();

        return new PaginatedData($taskDTOs, $count);
    }

    private function buildQuery(GetTasksQuery $query, int $userId): QueryBuilder
    {
        $taskTable = $this->em->getClassMetadata(Task::class)->getTableName();

        $qb = $this->em->getConnection()->createQueryBuilder()
            ->select('t.*')
            ->from($taskTable, 't')
            ->innerJoin('t', 'user', 'u', 'u.id = t.user_id')
            ->where('u.id = :userId')
            ->orderBy('t.created_at')
            ->setFirstResult($query->getPagination()->getOffset())
            ->setMaxResults($query->getPagination()->getLimit())
            ->setParameter('userId', $userId);

        if ($query->getExecutionDate() !== null) {
            $executionDay = $query->getExecutionDate()->setTime(0, 0);
            $qb->andWhere('t.execution_day >= :fromTime')
                ->andWhere('t.execution_day < :toTime')
                ->setParameter('fromTime', $executionDay->format(DateTimeFormat::MYSQL_FORMAT))
                ->setParameter('toTime', $executionDay->modify('+1 day')->format(DateTimeFormat::MYSQL_FORMAT));
        }

        if ($query->getSearchText() !== null) {
            $qb->andWhere('t.title LIKE :searchText OR t.description LIKE :searchText')
                ->setParameter('searchText', "%{$query->getSearchText()}%");
        }

        return $qb;
    }
}
