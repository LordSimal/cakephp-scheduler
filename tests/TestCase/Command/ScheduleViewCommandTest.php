<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Command;

use Cake\Collection\Collection;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;
use TestApp\Command\TestAppCommand;

class ScheduleViewCommandTest extends TestCase
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

    public function testRunScheduleView(): void
    {
        $this->exec('schedule:view');

        $this->assertExitSuccess();
        $this->assertOutputContains('0 0 * * 0 | TestApp\Command\TestAppCommand');
        $this->assertOutputContains('0 0 * * * | TestPlugin\Command\TestPluginCommand');
    }

    public function testRunScheduleViewWithEventsHavingArgsAndOptions(): void
    {
        $this->mockService(Scheduler::class, function () {
            $schedulerMock = $this->getMockBuilder(Scheduler::class)->getMock();

            $event = new Event(new TestAppCommand(), ['somearg', '--myoption=someoption']);
            $collection = new Collection([$event]);

            $schedulerMock->expects($this->any())
                ->method('allEvents')
                ->willReturn($collection);

            return $schedulerMock;
        });
        $this->exec('schedule:view');

        $this->assertExitSuccess();
        $this->assertOutputContains('* * * * * | TestApp\Command\TestAppCommand [somearg --myoption=someoption]');
    }

    public function testRunScheduleViewNoEvents(): void
    {
        $this->mockService(Scheduler::class, function () {
            $schedulerMock = $this->getMockBuilder(Scheduler::class)->getMock();

            $collection = new Collection([]);

            $schedulerMock->expects($this->any())
                ->method('allEvents')
                ->willReturn($collection);

            return $schedulerMock;
        });
        $this->exec('schedule:view');

        $this->assertExitSuccess();
        $this->assertOutputContains('No commands are configured.');
    }
}
