<?php
declare(strict_types=1);

namespace CakeScheduler\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;

class ScheduleRunCommand extends Command
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
        $events = $this->scheduler->dueEvents();

        if ($events->isEmpty()) {
            $io->info('No scheduled commands are ready to run.');

            return self::CODE_SUCCESS;
        }

        $events->each(function (Event $event) use ($io): void {
            $this->runEvent($event, $io);
        });

        return self::CODE_SUCCESS;
    }

    /**
     * @param \CakeScheduler\Scheduler\Event $event The event which should be executed
     * @param \Cake\Console\ConsoleIo $io The IO instance from this command
     * @return int|null
     */
    protected function runEvent(Event $event, ConsoleIo $io): ?int
    {
        $this->scheduler->dispatchEvent('CakeScheduler.beforeExecute', ['event' => $event]);
        $result = $event->run($io);
        $this->scheduler->dispatchEvent('CakeScheduler.afterExecute', ['event' => $event, 'result' => $result]);

        return $result;
    }
}
