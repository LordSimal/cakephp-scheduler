<?php
declare(strict_types=1);

namespace CakeScheduler;

use CakeScheduler\Scheduler\Scheduler;

/**
 * @psalm-suppress MissingInterfaceImmutableAnnotation
 */
interface CakeSchedulerInterface
{
    /**
     * @param \CakeScheduler\Scheduler\Scheduler $scheduler The scheduler instance
     * @return void
     * @psalm-suppress MissingAbstractPureAnnotation
     */
    public function schedule(Scheduler &$scheduler): void;
}
