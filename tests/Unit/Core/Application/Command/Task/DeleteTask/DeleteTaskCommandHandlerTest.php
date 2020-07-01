<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Task\DeleteTask;

use App\Core\Application\Command\Task\DeleteTask\DeleteTaskCommand;
use App\Core\Application\Command\Task\DeleteTask\DeleteTaskCommandHandler;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserFetcherInterface;
use App\Shared\Domain\Exception\AccessForbiddenException;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteTaskCommandHandlerTest extends TestCase
{
    public function test_it_throws_exception_when_task_not_found(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')->willReturn(null);

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new DeleteTaskCommand(1);
        $handler = new DeleteTaskCommandHandler($repository, $userFetcher);

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

        $command = new DeleteTaskCommand(1);
        $handler = new DeleteTaskCommandHandler($repository, $userFetcher);

        $handler($command);
    }

    public function test_it_deletes_when_invoked(): void
    {
        $title = 'Some title';
        $executionDay = (new \DateTimeImmutable())->setTime(0, 0);
        $description = 'Some description';

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')->willReturn(new Task($title, $executionDay, $this->getUser(), $description));
        $repository->expects(self::once())
            ->method('remove')
            ->with(self::callback(
                fn(Task $task): bool => $task->getTitle() === $title
                    && $task->getDescription() === $description
                    && $task->getExecutionDay() == $executionDay
            ));

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new DeleteTaskCommand(1);
        $handler = new DeleteTaskCommandHandler($repository, $userFetcher);
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
