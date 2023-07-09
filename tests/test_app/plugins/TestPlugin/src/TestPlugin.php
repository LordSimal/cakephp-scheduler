<?php

namespace TestPlugin;

use Cake\Core\BasePlugin;
use CakeScheduler\CakeSchedulerInterface;
use CakeScheduler\Scheduler\Scheduler;
use TestPlugin\Command\TestPluginCommand;

/**
 * Plugin for Queue
 */
class TestPlugin extends BasePlugin implements CakeSchedulerInterface {

    protected bool $middlewareEnabled = false;

    protected bool $consoleEnabled = false;

    protected bool $bootstrapEnabled = false;

    protected bool $routesEnabled = false;

    protected bool $servicesEnabled = false;

    /**
     * @inheritDoc
     */
    public function schedule(Scheduler &$scheduler): void
    {
        $scheduler->execute(TestPluginCommand::class)->daily();
    }
}