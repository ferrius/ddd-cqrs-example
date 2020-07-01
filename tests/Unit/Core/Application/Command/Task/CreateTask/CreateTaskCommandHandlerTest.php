<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Task\CreateTask;

use App\Core\Application\Command\Task\CreateTask\CreateTaskCommand;
use App\Core\Application\Command\Task\CreateTask\CreateTaskCommandHandler;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserFetcherInterface;
use PHPUnit\Framework\TestCase;

final class CreateTaskCommandHandlerTest extends TestCase
{
    public function test_it_creates_task_when_invoked(): void
    {
        $executionDay = (new \DateTimeImmutable())->setTime(0, 0);
        $title = 'Some title';
        $description = 'Some description';

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('add')
            ->with(self::callback(
                fn(Task $task): bool => $task->getTitle() === $title
                    && $task->getDescription() === $description
                    && $task->getExecutionDay() == $executionDay
            ));

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn(new User('name', 'pass_hash', $this->getUniqueUsernameSpecification()));

        $command = new CreateTaskCommand($title, $executionDay, $description);
        $handler = new CreateTaskCommandHandler($repository, $userFetcher);

        try {
            $handler($command);
        } catch (\Error $e) {
            // php7.4 fix
            if (strpos($e->getMessage(), 'id must not be accessed before initialization') === false) {
                throw $e;
            }
        }
    }

    private function getUniqueUsernameSpecification(): UniqueUsernameSpecificationInterface
    {
        $specification = $this->createMock(UniqueUsernameSpecificationInterface::class);
        $specification->method('isSatisfiedBy')->willReturn(true);

        return $specification;
    }
}
