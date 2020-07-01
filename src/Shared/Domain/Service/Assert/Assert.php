<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service\Assert;

use App\Shared\Domain\Exception\InvalidInputDataException;
use Webmozart\Assert\Assert as WebmozartAssert;

final class Assert extends WebmozartAssert
{
    public static function dateTimeString(string $value, string $format, string $message = ''): void
    {
        $date = \DateTimeImmutable::createFromFormat($format, $value);

        if ($date === false) {
            static::reportInvalidArgument(sprintf(
                $message === '' ? 'Date time string "%s" should be like "%s"' : $message,
                $value,
                $format
            ));
        }
    }

    /**
     * @param string $message
     *
     * @throws InvalidInputDataException
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidInputDataException($message);
    }
}
