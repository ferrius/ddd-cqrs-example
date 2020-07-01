<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Service\Assert\Assert;
use App\Shared\Infrastructure\Type\DateTimeFormat;
use Symfony\Component\HttpFoundation\Request;

final class ParamFetcher
{
    private const TYPE_STRING = 'string';
    private const TYPE_INT = 'int';
    private const TYPE_DATE = 'date';
    //TODO: need to add rest of scalar types

    private const SCALAR_TYPES = [self::TYPE_STRING, self::TYPE_INT];

    /**
     * @var array<string, mixed>
     */
    private array $data;

    private bool $testScalarType;

    /**
     * @param array<string, mixed> $data
     * @param bool $testScalarType
     */
    public function __construct(array $data, bool $testScalarType = true)
    {
        $this->data = $data;
        $this->testScalarType = $testScalarType;
    }

    public static function fromRequestAttributes(Request $request): self
    {
        return new self($request->attributes->all(), false);
    }

    public static function fromRequestBody(Request $request): self
    {
        return new self($request->request->all());
    }

    public static function fromRequestQuery(Request $request): self
    {
        return new self($request->query->all(), false);
    }

    public function getRequiredString(string $key): string
    {
        $this->assertRequired($key);
        $this->assertType($key, self::TYPE_STRING);

        return (string) $this->data[$key];
    }

    public function getNullableString(string $key): ?string
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        $this->assertType($key, self::TYPE_STRING);

        return (string) $this->data[$key];
    }

    public function getRequiredInt(string $key): int
    {
        $this->assertRequired($key);
        $this->assertType($key, self::TYPE_INT);

        return (int) $this->data[$key];
    }

    public function getNullableInt(string $key): ?int
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        $this->assertType($key, self::TYPE_INT);

        return (int) $this->data[$key];
    }

    public function getRequiredDate(string $key): \DateTimeImmutable
    {
        $this->assertRequired($key);
        $this->assertType($key, self::TYPE_DATE);

        return new \DateTimeImmutable($this->data[$key]);
    }

    public function getNullableDate(string $key): ?\DateTimeImmutable
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        $this->assertType($key, self::TYPE_DATE);

        return new \DateTimeImmutable($this->data[$key]);
    }

    // .....
    // TODO:  Add additional required methods for every scalar type
    // .....

    private function assertRequired(string $key): void
    {
        Assert::keyExists($this->data, $key, sprintf('"%s" not found', $key));
        Assert::notNull($this->data[$key], sprintf('"%s" should be not null', $key));
    }

    private function assertType(string $key, string $type): void
    {
        if (!$this->testScalarType && \in_array($type, self::SCALAR_TYPES, true)) {
            return;
        }

        switch ($type) {
            case self::TYPE_STRING:
                Assert::string($this->data[$key], sprintf('"%s" should be a string. Got %%s', $key));
                break;

            case self::TYPE_INT:
                Assert::string($this->data[$key], sprintf('"%s" should be an integer. Got %%s', $key));
                break;

            case self::TYPE_DATE:
                Assert::dateTimeString($this->data[$key], DateTimeFormat::DATE_FORMAT, sprintf('"%s" should be a valid format "%s" date', $key, DateTimeFormat::DATE_FORMAT));
                break;
        }
    }
}
