<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

final class HttpSpec
{
    public const STR_HTTP_OK = 'OK';
    public const STR_HTTP_BAD_REQUEST = 'Bad request';
    public const STR_HTTP_UNAUTHORIZED = 'Unauthorized';
    public const STR_HTTP_NOT_FOUND = 'Not found';
    public const STR_HTTP_CREATED = 'Created';
    public const STR_HTTP_NO_CONTENT = 'No content';

    public const HEADER_X_ITEMS_COUNT = 'X-Items-Count';
    public const HEADER_LOCATION = 'Location';
}
