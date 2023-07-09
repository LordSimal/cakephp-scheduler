<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Command;

use Cake\Collection\Collection;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;
use TestApp\Command\TestAppCommand;
use TestPlugin\Command\TestPluginCommand;

class ScheduleRunCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
        // Sets the TestApp namespace to be used instead of App
        $this->setAppNamespace();
        $this->configApplication(
            'TestApp\Application',
            [PLUGIN_TESTS . 'test_app' . DS . 'config']
        );
    }

    public function testRunNoCommand(): void
    {
        $this->mockService(Scheduler::class, function () {
            $schedulerMock = $this->getMockBuilder(Scheduler::class)->getMock();
            $collection = new Collection([]);

            $schedulerMock->expects($this->any())
                ->method('dueEvents')
                ->willReturn($collection);

            return $schedulerMock;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('No scheduled commands are ready to run.');
    }

    public function testRunSingleCommand(): void
    {
        $this->mockService(Scheduler::class, function () {
            $schedulerMock = $this->getMockBuilder(Scheduler::class)->getMock();

            $event = new Event(new TestAppCommand(), []);
            $collection = new Collection([$event]);

            $schedulerMock->expects($this->any())
                ->method('dueEvents')
                ->willReturn($collection);

            return $schedulerMock;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
    }

    public function testRunMultipleCommands(): void
    {
        $this->mockService(Scheduler::class, function () {
            $schedulerMock = $this->getMockBuilder(Scheduler::class)->getMock();

            $versionEvent = new Event(new TestAppCommand(), []);
            $routesEvent = new Event(new TestPluginCommand(), []);
            $collection = new Collection([$versionEvent, $routesEvent]);

            $schedulerMock->expects($this->any())
                ->method('dueEvents')
                ->willReturn($collection);

            return $schedulerMock;
        });
        $this->exec('schedule:run');

        $this->assertExitSuccess();
        $this->assertOutputContains('Executing [TestApp\\Command\\TestAppCommand]');
        $this->assertOutputContains('Test App Command executed');
        $this->assertOutputContains('Executing [TestPlugin\\Command\\TestPluginCommand]');
        $this->assertOutputContains('Test Plugin Command executed');
    }
}
