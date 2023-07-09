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

    /**
     * @var bool
     */
    protected $middlewareEnabled = false;

    /**
     * @var bool
     */
    protected $consoleEnabled = false;

    /**
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * @var bool
     */
    protected $servicesEnabled = false;

    /**
     * @inheritDoc
     */
    public function schedule(Scheduler &$scheduler): void
    {
        $scheduler->execute(TestPluginCommand::class)->daily();
    }
}