<?php

declare(strict_types=1);

namespace App\Core\Application\EventHandler\Task\LogTaskLiveCycleChanges;

use App\Core\Domain\Model\Task\TaskDeclinedEvent;
use Psr\Log\LoggerInterface;

final class TaskDeclinedEventHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(TaskDeclinedEvent $event): void
    {
        $this->logger->info(sprintf('Task %s was declined', $event->getTask()->getId()));
    }
}
