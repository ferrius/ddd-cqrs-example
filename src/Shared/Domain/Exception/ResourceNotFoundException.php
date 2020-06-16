<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Exception;

use App\Shared\Domain\Exception\InvalidInputDataException;

final class ResourceNotFoundException extends InvalidInputDataException
{
}
