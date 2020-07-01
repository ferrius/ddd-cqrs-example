<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Task\UpdateTask;

use App\Core\Application\Command\Task\UpdateTask\UpdateTaskCommand;
use App\Core\Application\Command\Task\UpdateTask\UpdateTaskCommandHandler;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserFetcherInterface;
use App\Shared\Domain\Exception\AccessForbiddenException;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateTaskCommandHandlerTest extends TestCase
{
    public function test_it_throws_exception_when_task_not_found(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')->willReturn(null);

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new UpdateTaskCommand(1, 'title', new \DateTimeImmutable());
        $handler = new UpdateTaskCommandHandler($repository, $userFetcher);

        $handler($command);
    }

    public function test_it_throws_exception_when_task_not_yours(): void
    {
        $this->expectException(AccessForbiddenException::class);

        $user = $this->createMock(User::class);
        $user->method('equals')->willReturn(false);

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')->willReturn(new Task('title', new \DateTimeImmutable(), $user));

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($user);

        $command = new UpdateTaskCommand(1, 'title', new \DateTimeImmutable());
        $handler = new UpdateTaskCommandHandler($repository, $userFetcher);

        $handler($command);
    }

    public function test_it_update_task_when_invoked(): void
    {
        $newTitle = 'new title';
        $newDescription = 'new description';
        $newExecutionDay = (new \DateTimeImmutable())->setTime(0, 0)->modify('+2 days');

        $repository = $this->createMock(TaskRepositoryInterface::class);

        $repository->method('find')
            ->willReturn(new Task('title', new \DateTimeImmutable(), $this->getUser()));

        $repository->expects(self::once())
            ->method('add')
            ->with(self::callback(fn(Task $task): bool =>
                $task->getTitle() === $newTitle
                && $task->getDescription() === $newDescription
                && $task->getExecutionDay() == $newExecutionDay
            ));

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new UpdateTaskCommand(1, $newTitle, $newExecutionDay, $newDescription);
        $handler = new UpdateTaskCommandHandler($repository, $userFetcher);

        $handler($command);
    }

    /**
     * @return User|MockObject
     */
    private function getUser(): MockObject
    {
        $user = $this->createMock(User::class);
        $user->method('equals')->willReturn(true);

        return $user;
    }
}
