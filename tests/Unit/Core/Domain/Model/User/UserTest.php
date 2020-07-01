<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Domain\Model\User;

use App\Core\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\Core\Domain\Model\User\User;
use App\Shared\Domain\Exception\InvalidInputDataException;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function test_it_throws_exception_when_password_to_long(): void
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Password should contain at most/');

        new User('admin', str_repeat('x', User::MAX_PASSWORD_LENGTH + 1), $this->getUniqueUsernameSpecification());
    }

    public function test_it_return_false_when_users_are_not_equals(): void
    {
        $userOne = new User('admin', 'some_hash', $this->getUniqueUsernameSpecification());
        $userTwo = new User('admin', 'some_hash', $this->getUniqueUsernameSpecification());
        $userThree = new User('admin', 'some_hash', $this->getUniqueUsernameSpecification());

        $this->setUserId($userOne, 1);
        $this->setUserId($userTwo, 2);
        $this->setUserId($userThree, 1);

        self::assertFalse($userOne->equals($userTwo));
        self::assertTrue($userOne->equals($userThree));
    }

    public function test_it_throws_exception_when_username_to_long(): void
    {
        $this->expectException(InvalidInputDataException::class);
        $this->expectDeprecationMessageMatches('/Username should contain at most/');

        new User(str_repeat('x', User::MAX_USER_NAME_LENGTH + 1), 'some_hash', $this->getUniqueUsernameSpecification());
    }

    public function test_it_creates_default_role_user(): void
    {
        $user = new User('admin', 'some_hash', $this->getUniqueUsernameSpecification());

        self::assertContains(User::DEFAULT_USER_ROLE, $user->getRoles());

        $user = new User('admin', 'some_hash', $this->getUniqueUsernameSpecification(), ['ROLE_ADMIN']);

        self::assertContains(User::DEFAULT_USER_ROLE, $user->getRoles());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_it_ok_when_valid_values_set(): void
    {
        new User(str_repeat('x', User::MAX_USER_NAME_LENGTH), str_repeat('x', User::MAX_PASSWORD_LENGTH), $this->getUniqueUsernameSpecification());
    }

    private function setUserId(User $user, int $id): void
    {
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, $id);
    }

    private function getUniqueUsernameSpecification(): UniqueUsernameSpecificationInterface
    {
        $specification = $this->createMock(UniqueUsernameSpecificationInterface::class);
        $specification->method('isSatisfiedBy')->willReturn(true);

        return $specification;
    }
}
