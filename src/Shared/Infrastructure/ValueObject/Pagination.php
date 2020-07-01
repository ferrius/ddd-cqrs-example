<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ValueObject;

use App\Shared\Domain\Service\Assert\Assert;
use Symfony\Component\HttpFoundation\Request;

final class Pagination
{
    public const DEFAULT_LIMIT = 50;
    public const DEFAULT_OFFSET = 0;

    public const LIMIT_NAME = 'limit';
    public const OFFSET_NAME = 'offset';

    private int $limit;

    private int $offset;

    public function __construct(int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET)
    {
        Assert::greaterThanEq($limit, 0);
        Assert::greaterThanEq($offset, 0);

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public static function fromRequest(Request $request): self
    {
        $limit = $request->get(self::LIMIT_NAME, self::DEFAULT_LIMIT);
        Assert::integerish($limit);

        $offset = $request->get(self::OFFSET_NAME, self::DEFAULT_OFFSET);
        Assert::integerish($offset);

        return new self((int) $limit, (int) $offset);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
