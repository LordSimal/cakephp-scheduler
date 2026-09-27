<?php
/**
 * @var \CakeScheduler\CakeSchedulerPlugin $this
 * @var \Cake\Http\BaseApplication $app
 */

use Cake\Core\PluginApplicationInterface;
use CakeScheduler\CakeSchedulerInterface;
use CakeScheduler\Scheduler\Scheduler;

$container = $app->getContainer();
$scheduler = $container->get(Scheduler::class);

if ($app instanceof CakeSchedulerInterface) {
    $app->schedule($scheduler);
}

if ($app instanceof PluginApplicationInterface) {
    foreach ($app->getPlugins() as $plugin) {
        if ($plugin instanceof CakeSchedulerInterface) {
            $plugin->schedule($scheduler);
        }
    }
}
