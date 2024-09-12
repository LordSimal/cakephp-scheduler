<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Command;

use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Container;
use Cake\Event\EventInterface;
use Cake\TestSuite\LogTestTrait;
use Cake\TestSuite\TestCase;
use CakeScheduler\Error\SchedulerStoppedException;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;
use Exception;
use Mockery;
use Mockery\LegacyMockInterface;
use TestApp\Command\TestAppCommand;
use TestPlugin\Command\TestPluginCommand;

class ScheduleRunCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use LogTestTrait;

    protected Scheduler|LegacyMockInterface $scheduler;

    protected function setUp(): void
    {
        parent::setUp();
        // Sets the TestApp namespace to be used instead of App
        $this->setAppNamespace();
        $this->configApplication(
            'TestApp\Application',
            [PLUGIN_TESTS . 'test_app' . DS . 'config']
        );

        $this->scheduler = Mockery::mock(Scheduler::class, [new Container()])->makePartial();
    }

    public function testRunNoCommand(): void
    {
        $this->mockService(Scheduler::class, function () {
            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn(new Collection([]));

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('No scheduled commands are ready to run.');
    }

    public function testRunSingleCommand(): void
    {
        $this->mockService(Scheduler::class, function () {
            $event = new Event(new TestAppCommand(), []);
            $collection = new Collection([$event]);
            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
    }

    public function testRunMultipleCommands(): void
    {
        $this->mockService(Scheduler::class, function () {
            $appEvent = new Event(new TestAppCommand(), []);
            $pluginEvent = new Event(new TestPluginCommand(), []);
            $collection = new Collection([$appEvent, $pluginEvent]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
        $this->assertOutputContains('Executing [TestPlugin\\Command\\TestPluginCommand]');
        $this->assertOutputContains('Test Plugin Command executed');
    }

    public function testRunSingleCommandWithArgsAndOptions(): void
    {
        $this->mockService(Scheduler::class, function () {
            $event = new Event(new TestAppCommand(), ['somearg', '--myoption=someoption']);
            $collection = new Collection([$event]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
        $this->assertOutputContains('with arg somearg');
        $this->assertOutputContains('with option someoption');
    }

    public function testRunSingleCommandWhichThrowsException(): void
    {
        $this->mockService(Scheduler::class, function () {
            $command = new class () extends TestAppCommand {
                public function execute(Arguments $args, ConsoleIo $io): void
                {
                    throw new Exception('Test Exception');
                }
            };

            $event = new Event($command, []);
            $collection = new Collection([$event]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand@anonymous');
        $this->assertErrorContains('Test Exception');
    }

    public function testRunSingleCommandWhichThrowsExceptionAndListenerStopsExecution(): void
    {
        $this->mockService(Scheduler::class, function () {
            $command = new class () extends TestAppCommand {
                public function execute(Arguments $args, ConsoleIo $io): void
                {
                    throw new Exception('Test Exception');
                }
            };

            $event = new Event($command, []);
            $collection = new Collection([$event]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            $this->scheduler->getEventManager()->on('CakeScheduler.errorExecute', function (EventInterface $event) {
                $event->setResult(Scheduler::SHOULD_STOP_EXECUTION);
            });

            return $this->scheduler;
        });

        $this->expectException(SchedulerStoppedException::class);
        $this->expectExceptionMessage('Scheduler was stopped by event listener');
        $this->exec('schedule:run');
    }

    public function testRunMultipleCommandsAndLastOneFails(): void
    {
        $this->mockService(Scheduler::class, function () {
            $appEvent = new Event(new TestAppCommand(), []);
            $pluginEvent = new Event(new TestPluginCommand(), []);
            $failCommand = new class () extends TestAppCommand {
                public function execute(Arguments $args, ConsoleIo $io): void
                {
                    throw new Exception('Test Exception');
                }
            };
            $failEvent = new Event($failCommand, []);
            $collection = new Collection([$appEvent, $pluginEvent, $failEvent]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
        $this->assertOutputContains('Executing [TestPlugin\\Command\\TestPluginCommand]');
        $this->assertOutputContains('Test Plugin Command executed');
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand@anonymous');
        $this->assertErrorContains('Test Exception');
    }

    public function testRunMultipleCommandsAndSecondToLastOneFailsAndStopsExecution(): void
    {
        $this->mockService(Scheduler::class, function () {
            $appEvent = new Event(new TestAppCommand(), []);
            $pluginEvent = new Event(new TestPluginCommand(), []);
            $failCommand = new class () extends TestAppCommand {
                public function execute(Arguments $args, ConsoleIo $io): void
                {
                    throw new Exception('Test Exception');
                }
            };
            $failEvent = new Event($failCommand, []);
            $collection = new Collection([$appEvent, $failEvent, $pluginEvent]);

            $this->scheduler->shouldReceive('dueEvents')
                ->andReturn($collection);

            $this->scheduler->getEventManager()->on('CakeScheduler.errorExecute', function (EventInterface $event) {
                $event->setResult(Scheduler::SHOULD_STOP_EXECUTION);
            });

            return $this->scheduler;
        });

        $this->expectException(SchedulerStoppedException::class);
        $this->expectExceptionMessage('Scheduler was stopped by event listener');
        $this->exec('schedule:run');

        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand@anonymous');
        $this->assertErrorContains('Test Exception');

        $this->assertOutputNotContains('Executing [TestPlugin\\Command\\TestPluginCommand]');
        $this->assertOutputNotContains('Test Plugin Command executed');
    }
}
