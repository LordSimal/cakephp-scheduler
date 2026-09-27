<?php
declare(strict_types=1);

namespace CakeScheduler\Command;

use Cake\Command\Command;
use CakeScheduler\Error\SchedulerStoppedException;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;
use Throwable;

class ScheduleRunCommand extends Command
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
        $events = $this->scheduler->dueEvents();

        if ($events->isEmpty()) {
            $this->io->info('No scheduled commands are ready to run.');

            return self::CODE_SUCCESS;
        }

        $events->each(function (Event $event): void {
            $returnCode = $this->runEvent($event);

            if ($returnCode === Scheduler::SHOULD_STOP_EXECUTION) {
                $this->io->error('Error while executing command: ' . get_class($event->getCommand()));

                throw new SchedulerStoppedException('Scheduler was stopped by event listener');
            }
        });

        return self::CODE_SUCCESS;
    }

    /**
     * @param \CakeScheduler\Scheduler\Event $event The event which should be executed
     * @return int|null
     */
    protected function runEvent(Event $event): ?int
    {
        $this->scheduler->dispatchEvent('CakeScheduler.beforeExecute', ['event' => $event]);
        try {
            $result = $event->run($this->io);
            $this->scheduler->dispatchEvent('CakeScheduler.afterExecute', ['event' => $event, 'result' => $result]);
        } catch (Throwable $exception) {
            $this->io->error($exception->getMessage());
            $event = $this->scheduler->dispatchEvent('CakeScheduler.errorExecute', [
                'event' => $event,
                'exception' => $exception,
            ]);
            $result = $event->getResult();
        }

        return $result;
    }
}
