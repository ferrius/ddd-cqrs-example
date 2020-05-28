<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

use App\Core\Domain\Model\User\User;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Infrastructure\Assert\Assert;
use App\Shared\Infrastructure\Exception\BusinessLogicViolationException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Task extends AggregateRoot
{
    public const MIN_TITLE_LENGTH = 5;
    public const MAX_TITLE_LENGTH = 100;
    public const MAX_DESCRIPTION_LENGTH = 100;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Embedded(class="App\Core\Domain\Model\Task\Status", columnPrefix=false)
     */
    private Status $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Domain\Model\User\User")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $executionDay;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(string $title, \DateTimeImmutable $executionDay, User $user, string $description = '')
    {
        $this->setTitle($title);
        $this->setExecutionDay($executionDay);
        $this->setUser($user);
        $this->setDescription($description);
        $this->setStatus(Status::NEW());
        $this->setCreatedAt(new \DateTimeImmutable());

        $this->raise(new TaskCreatedEvent($this));
    }

    // API

    public function changeTitle(string $title): void
    {
        $this->setTitle($title);
    }

    public function changeDescription(string $description): void
    {
        $this->setDescription($description);
    }

    public function changeExecutionDay(\DateTimeImmutable $assignedDay): void
    {
        $this->setExecutionDay($assignedDay);
    }

    public function done(): void
    {
        if ($this->status->is(Status::DONE)) {
            return;
        }

        if ($this->status->is(Status::DECLINED)) {
            throw new BusinessLogicViolationException('Declined task can\'t be done');
        }

        $this->raise(new TaskDoneEvent($this));
        $this->setStatus(Status::DONE());
    }

    public function decline(): void
    {
        if ($this->status->is(Status::DECLINED)) {
            return;
        }

        if ($this->status->is(Status::DONE)) {
            throw new BusinessLogicViolationException('Done task can\'t be declined');
        }

        $this->raise(new TaskDeclinedEvent($this));
        $this->setStatus(Status::DECLINED());
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExecutionDay(): \DateTimeImmutable
    {
        return $this->executionDay;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Private Setters

    private function setTitle(string $title): void
    {
        Assert::minLength($title, self::MIN_TITLE_LENGTH, 'Title should contain at least %2$s characters. Got: %s');
        Assert::maxLength($title, self::MAX_TITLE_LENGTH, 'Title should contain at most %2$s characters. Got: %s');
        $this->title = $title;
    }

    private function setDescription(string $description): void
    {
        Assert::maxLength($description, self::MAX_DESCRIPTION_LENGTH, 'Description should contain at most %2$s characters. Got: %s');
        $this->description = $description;
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    private function setExecutionDay(\DateTimeImmutable $executionDay): void
    {
        $executionDayNormalized = $executionDay->setTime(0, 0);
        $now = (new \DateTimeImmutable())->setTime(0, 0);

        Assert::greaterThanEq($executionDayNormalized, $now, 'Execution day should be not in past');

        $this->executionDay = $executionDayNormalized;
    }

    private function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
