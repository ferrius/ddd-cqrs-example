<?php

declare(strict_types=1);

namespace App\Core\Ports\Cli;

use App\Core\Application\Command\User\CreateUser\CreateUserCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * For internal usage
 */
final class AddUserCommand extends Command
{
    use HandleTrait;

    public const MIN_PASSWORD_LENGTH = 5;

    protected static $defaultName = 'app:create-user';

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter the username [admin] : ', 'admin');
        $userName = (string) $helper->ask($input, $output, $question);

        if ($userName === '') {
            $output->writeln('<error>User name should be not blank</error>');
        }

        $question = new Question('Please enter the password : ');
        $question->setHidden(true);
        $password = (string) $helper->ask($input, $output, $question);

        if (\strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $output->writeln('<error>Password is to short, need more than 4 symbols (bytes)</error>');
        }

        $question = new Question('Please repeat the password : ');
        $question->setHidden(true);
        $passwordRepeat = (string) $helper->ask($input, $output, $question);

        if ($password !== $passwordRepeat) {
            $output->writeln('<error>Passwords dont match</error>');
        }

        $this->handle(new CreateUserCommand($userName, $password));

        $output->writeln('<info>User created</info>');

        return 0;
    }
}
