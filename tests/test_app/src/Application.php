<?php
declare(strict_types=1);

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use CakeScheduler\CakeSchedulerInterface;
use CakeScheduler\CakeSchedulerPlugin;
use CakeScheduler\Scheduler\Scheduler;
use TestApp\Command\TestAppCommand;
use TestPlugin\TestPlugin;

class Application extends BaseApplication implements CakeSchedulerInterface
{
    public function bootstrap(): void
    {
        parent::bootstrap();

        $this->addPlugin(CakeSchedulerPlugin::class);
        $this->addPlugin(TestPlugin::class);
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
    }

    /**
     * @inheritDoc
     */
    public function schedule(Scheduler &$scheduler): void
    {
        $scheduler->execute(TestAppCommand::class)->weekly();
    }
}