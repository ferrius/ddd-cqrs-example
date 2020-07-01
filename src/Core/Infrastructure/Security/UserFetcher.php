<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Security;

use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserFetcherInterface;
use Symfony\Component\Security\Core\Security;

final class UserFetcher implements UserFetcherInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function fetchRequiredUser(): User
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new \InvalidArgumentException('Current user not found check security access list');
        }

        if (!($user instanceof User)) {
            throw new \InvalidArgumentException(sprintf('Invalid user type %s', \get_class($user)));
        }

        return $user;
    }

    public function fetchNullableUser(): ?user
    {
        $user = $this->security->getUser();

        if ($user === null) {
            return null;
        }

        if (!($user instanceof User)) {
            throw new \InvalidArgumentException(sprintf('Invalid user type %s', \get_class($user)));
        }

        return $user;
    }
}
