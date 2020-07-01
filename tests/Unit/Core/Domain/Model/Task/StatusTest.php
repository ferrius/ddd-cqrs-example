<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\Task;

use App\Core\Domain\Model\Task\Status;
use App\Shared\Domain\Exception\InvalidInputDataException;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function test_it_throws_exception_when_invalid_value_set(): void
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Status value should be one of/');

        new Status('some_invalid_status');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_it_ok_when_valid_value_set(): void
    {
        foreach (Status::VALID_STATUSES as $status) {
            new Status($status);
        }
    }
}
