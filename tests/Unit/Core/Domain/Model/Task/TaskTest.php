<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Task;

use App\Core\Domain\Model\Task\Status;
use App\Core\Domain\Model\Task\Task;
use App\Core\Domain\Model\Task\TaskCreatedEvent;
use App\Core\Domain\Model\Task\TaskDeclinedEvent;
use App\Core\Domain\Model\Task\TaskDoneEvent;
use App\Core\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\Core\Domain\Model\User\User;
use App\Shared\Domain\Exception\BusinessLogicViolationException;
use App\Shared\Domain\Exception\InvalidInputDataException;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function test_it_throws_exception_when_task_created_and_title_too_short(): void
    {
        new Task($this->getShortTitle(), new \DateTimeImmutable(), $this->getUser());
    }

    public function test_it_throws_exception_when_title_changed_and_title_too_short(): void
    {
        $task = $this->getTask();
        $task->changeTitle($this->getShortTitle());
    }

    public function test_it_throws_exception_when_task_created_and_title_too_long(): void
    {
        new Task($this->getLongTitle(), new \DateTimeImmutable(), $this->getUser());
    }

    public function test_it_throws_exception_when_title_changed_and_title_too_long(): void
    {
        $task = $this->getTask();
        $task->changeTitle($this->getLongTitle());
    }

    public function test_it_throws_exception_when_title_changed_and_description_too_long(): void
    {
        new Task(str_repeat('x', Task::MAX_TITLE_LENGTH), new \DateTimeImmutable(), $this->getUser(), $this->getLongDescription());
    }

    public function test_it_throws_exception_when_description_changed_and_description_too_long(): void
    {
        $task = $this->getTask();
        $task->changeDescription($this->getLongDescription());
    }

    public function test_it_throws_exception_when_created_and_execution_time_in_past(): void
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectExceptionMessage('Execution day should be not in past');

        new Task('title', (new \DateTimeImmutable())->modify('-1 day'), $this->getUser());
    }

    public function test_it_throws_exception_when_changed_and_execution_time_in_past(): void
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectExceptionMessage('Execution day should be not in past');

        $task = $this->getTask();
        $task->changeExecutionDay((new \DateTimeImmutable())->modify('-1 day'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_it_ok_when_valid_values_set(): void
    {
        $task = new Task(str_repeat('x', Task::MAX_TITLE_LENGTH), new \DateTimeImmutable(), $this->getUser(), str_repeat('x', Task::MAX_DESCRIPTION_LENGTH));

        $task->changeTitle(str_repeat('y', Task::MAX_TITLE_LENGTH));
        $task->changeDescription(str_repeat('y', Task::MAX_DESCRIPTION_LENGTH));
    }

    public function test_it_creates_new_status_when_task_is_created(): void
    {
        $task = $this->getTask();
        self::assertTrue($task->getStatus()->is(Status::NEW));
    }

    public function test_it_throws_exception_when_done_declined_task(): void
    {
        $this->expectException(BusinessLogicViolationException::class);
        $this->expectExceptionMessage('Declined task can\'t be done');

        $task = $this->getTask();
        $task->decline();
        self::assertTrue($task->getStatus()->is(Status::DECLINED));
        $task->done();
    }

    public function test_it_throws_exception_when_decline_done_task(): void
    {
        $this->expectException(BusinessLogicViolationException::class);
        $this->expectExceptionMessage('Done task can\'t be declined');

        $task = $this->getTask();
        $task->done();
        self::assertTrue($task->getStatus()->is(Status::DONE));
        $task->decline();
    }

    public function test_it_raises_event_when_task_created(): void
    {
        $task = $this->getTask();
        $events = $task->popEvents();

        self::assertContainsEquals(new TaskCreatedEvent($task), $events);
    }

    public function test_it_raises_event_when_task_becomes_done(): void
    {
        $task = $this->getTask();
        $task->done();
        $events = $task->popEvents();

        self::assertContainsEquals(new TaskDoneEvent($task), $events);
    }

    public function test_it_raises_event_when_task_becomes_declined(): void
    {
        $task = $this->getTask();
        $task->decline();
        $events = $task->popEvents();

        self::assertContainsEquals(new TaskDeclinedEvent($task), $events);
    }

    private function getTask(): Task
    {
        return new Task(str_repeat('x', Task::MAX_TITLE_LENGTH), new \DateTimeImmutable(), $this->getUser());
    }

    private function getShortTitle(): string
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Title should contain at least/');

        return str_repeat('x', Task::MIN_TITLE_LENGTH - 1);
    }

    private function getLongTitle(): string
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Title should contain at most/');

        return str_repeat('x', Task::MAX_TITLE_LENGTH +1);
    }

    private function getLongDescription(): string
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Description should contain at most/');

        return str_repeat('x', Task::MAX_DESCRIPTION_LENGTH + 1);
    }

    private function getUser(): User
    {
        $specification = $this->createMock(UniqueUsernameSpecificationInterface::class);
        $specification->method('isSatisfiedBy')->willReturn(true);

        return new User('Test', 'pass_hash', $specification);
    }
}
