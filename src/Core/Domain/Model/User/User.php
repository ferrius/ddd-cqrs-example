<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\User;

use App\Shared\Infrastructure\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 */
class User implements UserInterface
{
    public const DEFAULT_USER_ROLE = 'ROLE_USER';
    public const MAX_USER_NAME_LENGTH = 180;
    public const MAX_PASSWORD_LENGTH = 255;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @var array<int, string>
     *
     * @ORM\Column(type="json", nullable=false)
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default"="CURRENT_TIMESTAMP"}, nullable=false)
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @param string $username
     * @param string $password
     * @param string[] $roles
     */
    public function __construct(string $username, string $password, array $roles = [self::DEFAULT_USER_ROLE])
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setRoles($roles);
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function equals(User $user): bool
    {
        return $user->getId() === $this->getId();
    }

    // Setters

    private function setPassword(string $password): void
    {
        Assert::maxLength($password, self::MAX_PASSWORD_LENGTH, 'Password should contain at most %2$s characters. Got: %s');
        $this->password = $password;
    }

    private function setUsername(string $username): void
    {
        Assert::maxLength($username, self::MAX_USER_NAME_LENGTH, 'Username should contain at most %2$s characters. Got: %s');
        $this->username = $username;
    }

    private function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<int, string> $roles
     */
    private function setRoles(array $roles): void
    {
        if (!\in_array(self::DEFAULT_USER_ROLE, $roles, true)) {
            $roles[] = self::DEFAULT_USER_ROLE;
        }

        $this->roles = array_unique($roles);
    }
}
