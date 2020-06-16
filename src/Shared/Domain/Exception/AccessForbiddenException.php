<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use App\Shared\Infrastructure\Exception\InvalidInputDataException;

final class AccessForbiddenException extends InvalidInputDataException
{
}
