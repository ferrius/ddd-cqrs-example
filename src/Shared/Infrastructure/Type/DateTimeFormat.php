<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Type;

interface DateTimeFormat
{
    public const DATE_FORMAT = 'Y-m-d';
    public const MYSQL_FORMAT = 'Y-m-d H:i:s';
}
