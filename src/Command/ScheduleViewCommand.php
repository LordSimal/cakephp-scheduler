<?php
declare(strict_types=1);

namespace CakeScheduler\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
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
     * @param \Cake\Console\Arguments $args The args given to this command
     * @param \Cake\Console\ConsoleIo $io The io instance associated ot this command
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $events = $this->scheduler->allEvents();

        if ($events->isEmpty()) {
            $io->info('No commands are configured.');

            return self::CODE_SUCCESS;
        }

        $events->each(function (Event $event) use ($io): void {
            $expression = $event->getExpression();
            $fqcn = get_class($event->getCommand());
            $args = $event->getArgs();
            if ($args) {
                $fqcn .= ' [' . implode(' ', $args) . ']';
            }
            $msg = sprintf('%s | %s', $expression, $fqcn);
            $io->info($msg);
        });

        return self::CODE_SUCCESS;
    }
}
