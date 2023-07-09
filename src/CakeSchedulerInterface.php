<?php
declare(strict_types=1);

namespace CakeScheduler;

use CakeScheduler\Scheduler\Scheduler;

interface CakeSchedulerInterface
{
    /**
     * @param \CakeScheduler\Scheduler\Scheduler $scheduler The scheduler instance
     * @return void
     */
    public function schedule(Scheduler &$scheduler): void;
}
