<?php
declare(strict_types=1);

namespace CakeScheduler\Scheduler;

use Cake\Chronos\Chronos;
use Cake\Console\CommandInterface;
use Cake\Console\ConsoleIo;
use CakeScheduler\Scheduler\Traits\FrequenciesTrait;
use Cron\CronExpression;

class Event
{
    use FrequenciesTrait;

    protected CommandInterface $command;

    protected array $args;

    protected ConsoleIo $io;

    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    /**
     * @param \Cake\Console\CommandInterface $command The command object related to this event
     * @param array $args Args which should be passed to the command
     */
    public function __construct(CommandInterface $command, array $args = [])
    {
        $this->command = $command;
        $this->args = $args;
    }

    /**
     * @return bool
     */
    public function isDue(): bool
    {
        $dateTime = new Chronos();

        return (new CronExpression($this->expression))->isDue($dateTime->toDateTimeString());
    }

    /**
     * @param \Cake\Console\ConsoleIo $io The IO instance from the schedule:run command
     * @return int|null
     */
    public function run(ConsoleIo $io): ?int
    {
        $io->info(sprintf('Executing [%s]', $this->command::class));

        return $this->command->run($this->args, $io);
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @return \Cake\Console\CommandInterface
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }
}