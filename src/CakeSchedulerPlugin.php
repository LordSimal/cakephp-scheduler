<?php
declare(strict_types=1);

namespace CakeScheduler;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\ContainerInterface;
use CakeScheduler\Command\ScheduleRunCommand;
use CakeScheduler\Command\ScheduleViewCommand;
use CakeScheduler\Scheduler\Scheduler;

class CakeSchedulerPlugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    public function services(ContainerInterface $container): void
    {
        $container->add(ScheduleRunCommand::class)
            ->addArgument(Scheduler::class);
        $container->add(ScheduleViewCommand::class)
            ->addArgument(Scheduler::class);
    }

    /**
     * @inheritDoc
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands->add('schedule:run', ScheduleRunCommand::class)
            ->add('schedule:view', ScheduleViewCommand::class);
    }
}
