<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

use App\Core\Domain\Model\User\User;
use App\Shared\Domain\Exception\BusinessLogicViolationException;
use App\Shared\Domain\Model\Aggregate;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="task_status_idx", columns={"status"})})
 */
class Task extends Aggregate
{
    use TaskGS;

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

        $this->setStatus(Status::DONE());
        $this->raise(new TaskDoneEvent($this));
    }

    public function decline(): void
    {
        if ($this->status->is(Status::DECLINED)) {
            return;
        }

        if ($this->status->is(Status::DONE)) {
            throw new BusinessLogicViolationException('Done task can\'t be declined');
        }

        $this->setStatus(Status::DECLINED());
        $this->raise(new TaskDeclinedEvent($this));
    }
}
