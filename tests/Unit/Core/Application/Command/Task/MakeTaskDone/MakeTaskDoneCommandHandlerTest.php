<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Application\Command\Task\MakeTaskDone;

use App\Core\Application\Command\Task\MakeTaskDone\MakeTaskDoneCommand;
use App\Core\Application\Command\Task\MakeTaskDone\MakeTaskDoneCommandHandler;
use App\Core\Domain\Model\Task\Status;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskRepositoryInterface;
use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserFetcherInterface;
use App\Shared\Domain\Exception\AccessForbiddenException;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MakeTaskDoneCommandHandlerTest extends TestCase
{
    public function test_it_throws_exception_when_task_not_found(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')->willReturn(null);

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new MakeTaskDoneCommand(1);
        $handler = new MakeTaskDoneCommandHandler($repository, $userFetcher);

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

        $command = new MakeTaskDoneCommand(1);
        $handler = new MakeTaskDoneCommandHandler($repository, $userFetcher);

        $handler($command);
    }

    public function test_it_make_task_declined_when_invoked(): void
    {
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->method('find')
            ->willReturn(new Task('title', new \DateTimeImmutable(), $this->getUser()));
        $repository->expects(self::once())
            ->method('add')
            ->with(self::callback(fn(Task $task): bool => $task->getStatus()->is(Status::DONE)));

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchRequiredUser')->willReturn($this->getUser());

        $command = new MakeTaskDoneCommand(1);
        $handler = new MakeTaskDoneCommandHandler($repository, $userFetcher);

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
