<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User\CreateUser;

use App\Core\Domain\Model\User\User;
use App\Core\Infrastructure\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

final class CreateUserCommandHandler
{
    private EncoderFactoryInterface $encoderFactory;

    private UserRepository $userRepository;

    public function __construct(EncoderFactoryInterface $encoderFactory, UserRepository $userRepository)
    {
        $this->encoderFactory = $encoderFactory;
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $encoder = $this->encoderFactory->getEncoder(new User('', ''));
        $user = new User($command->getUsername(), $encoder->encodePassword($command->getPassword(), null));
        $this->userRepository->add($user);
    }
}
