<?php
declare(strict_types=1);

namespace CakeScheduler\Scheduler;

use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\Console\CommandInterface;
use Cake\Core\Container;
use Cake\Core\ContainerInterface;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @implements \Cake\Event\EventDispatcherInterface<\CakeScheduler\Scheduler\Scheduler>
 */
class Scheduler implements EventDispatcherInterface
{
    /**
     * @use \Cake\Event\EventDispatcherTrait<\CakeScheduler\Scheduler\Scheduler>
     */
    use EventDispatcherTrait;

    protected ?ContainerInterface $container = null;

    /**
     * All the events on the schedule.
     *
     * @var \Cake\Collection\CollectionInterface
     */
    protected CollectionInterface $events;

    /**
     * @param \Cake\Core\Container|null $container The DI container instance from the app
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;
        $this->events = new Collection([]);
    }

    /**
     * @param string $command The FQCN of the command to be executed
     * @param array $args Args which should be passed on to the command
     * @return \CakeScheduler\Scheduler\Event
     */
    public function execute(string $command, array $args = []): Event
    {
        try {
            $commandObj = $this->container->get($command);
        } catch (ContainerExceptionInterface | NotFoundExceptionInterface $ex) {
            if (class_exists($command)) {
                $commandObj = new $command();
            }
        }

        if (!isset($commandObj)) {
            throw new InvalidArgumentException(sprintf('Command `%s` not found', $command));
        }

        return $this->addCommand($commandObj, $args);
    }

    /**
     * @param \Cake\Console\CommandInterface $command The command instance which should be executed
     * @param array $args Args which should be passed on to the command
     * @return \CakeScheduler\Scheduler\Event
     */
    protected function addCommand(CommandInterface $command, array $args = []): Event
    {
        $event = new Event($command, $args);
        $this->events = $this->events->appendItem($event);

        return $event;
    }

    /**
     * @return \Cake\Collection\CollectionInterface
     */
    public function dueEvents(): CollectionInterface
    {
        return $this->events->filter(function (Event $event) {
            return $event->isDue();
        });
    }

    /**
     * @return \Cake\Collection\CollectionInterface
     */
    public function allEvents(): CollectionInterface
    {
        return $this->events;
    }
}
