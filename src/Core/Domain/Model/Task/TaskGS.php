<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

use App\Core\Domain\Model\User\User;
use App\Shared\Domain\Service\Assert\Assert;

trait TaskGS
{
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
