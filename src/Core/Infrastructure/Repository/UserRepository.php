<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\Model\User\User;
use App\Core\Domain\Model\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function findUserByUserName(string $username): ?User
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.username = :username')
            ->setParameters(['username' => $username])
            ->getQuery()->getOneOrNullResult();
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
