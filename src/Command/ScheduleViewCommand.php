<?php
declare(strict_types=1);

namespace CakeScheduler\Command;

use Cake\Command\Command;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;

class ScheduleViewCommand extends Command
{
    /**
     * @param \CakeScheduler\Scheduler\Scheduler $scheduler DI injected
     */
    public function __construct(protected Scheduler $scheduler)
    {
    }

    /**
     * @return int
     */
    public function execute(): int
    {
        $events = $this->scheduler->allEvents();

        if ($events->isEmpty()) {
            $this->io->info('No commands are configured.');

            return self::CODE_SUCCESS;
        }

        $events->each(function (Event $event): void {
            $expression = $event->getExpression();
            $fqcn = get_class($event->getCommand());
            $args = $event->getArgs();
            if ($args) {
                $fqcn .= ' [' . implode(' ', $args) . ']';
            }
            $msg = sprintf('%s | %s', $expression, $fqcn);
            $this->io->info($msg);
        });

        return self::CODE_SUCCESS;
    }
}
