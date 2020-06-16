<?php

declare(strict_types=1);

namespace App\Core\Application\EventHandler\Task\LogTaskLiveCycleChanges;

use App\Core\Domain\Model\Task\TaskDoneEvent;
use Psr\Log\LoggerInterface;

final class TaskDoneEventHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(TaskDoneEvent $event): void
    {
        $this->logger->info(sprintf('Task %s was done', $event->getTask()->getId()));
    }
}
