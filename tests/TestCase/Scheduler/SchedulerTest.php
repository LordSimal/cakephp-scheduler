<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Scheduler;

use Cake\Chronos\Chronos;
use Cake\Command\VersionCommand;
use Cake\Console\Command\HelpCommand;
use Cake\Console\ConsoleIo;
use Cake\Core\Container;
use Cake\TestSuite\TestCase;
use CakeScheduler\Scheduler\Scheduler;
use InvalidArgumentException;

class SchedulerTest extends TestCase
{
    protected Scheduler $scheduler;

    protected function setUp(): void
    {
        $container = new Container();
        $container->add(VersionCommand::class);
        $container->add(HelpCommand::class);
        $this->scheduler = new Scheduler($container);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->scheduler);
    }

    public function testAddDueEvents(): void
    {
        $this->scheduler->execute(VersionCommand::class);
        $events = $this->scheduler->dueEvents();
        $this->assertNotEmpty($events);
    }

    public function testAddMultipleDueEvents(): void
    {
        Chronos::setTestNow('2023-07-08 12:00:00');
        $this->scheduler->execute(VersionCommand::class);
        $this->scheduler->execute(HelpCommand::class)->everyXHours(2);
        $events = $this->scheduler->dueEvents();
        $this->assertEquals(2, $events->count());
        Chronos::setTestNow('now');
    }

    public function testAddCallable(): void
    {
        $this->scheduler->execute(function ($a, $b, $c, ConsoleIo $io) {
            $io->info('Sum');

            return $a + $b + $c;
        }, [1,2,3]);
        $events = $this->scheduler->dueEvents();
        $this->assertNotEmpty($events);
    }

    public function testAddUnknownCommand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->scheduler->execute('UnknownCommand');
    }
}
