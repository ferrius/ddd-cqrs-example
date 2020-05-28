<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Security;

use App\Core\Domain\Model\User\User;

interface UserFetcherInterface
{
    public function fetchRequiredUser(): User;

    public function fetchNullableUser(): ?user;
}
