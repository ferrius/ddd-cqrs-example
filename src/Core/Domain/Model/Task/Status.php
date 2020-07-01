<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Task;

use App\Shared\Domain\Service\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const NEW = 'new';
    public const DECLINED = 'declined';
    public const DONE = 'done';
    public const VALID_STATUSES = [self::NEW, self::DECLINED, self::DONE];

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private string $status;

    public function __construct(string $status)
    {
        Assert::inArray($status, self::VALID_STATUSES, 'Status value should be one of: %2$s. Got: %s');

        $this->status = $status;
    }

    public function __toString(): string
    {
        return $this->status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public static function NEW(): self
    {
        return new self(self::NEW);
    }

    public static function DECLINED(): self
    {
        return new self(self::DECLINED);
    }

    public static function DONE(): self
    {
        return new self(self::DONE);
    }

    public function is(string $status): bool
    {
        return $this->status === $status;
    }
}
