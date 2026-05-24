<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Command;

use Cake\Collection\Collection;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Container;
use Cake\TestSuite\TestCase;
use CakeScheduler\Scheduler\Event;
use CakeScheduler\Scheduler\Scheduler;
use Mockery;
use Mockery\MockInterface;
use TestApp\Command\TestAppCommand;

class ScheduleViewCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    protected Scheduler&MockInterface $scheduler;

    protected function setUp(): void
    {
        parent::setUp();
        // Sets the TestApp namespace to be used instead of App
        $this->setAppNamespace();
        $this->configApplication(
            'TestApp\Application',
            [PLUGIN_TESTS . 'test_app' . DS . 'config'],
        );

        /** @var \CakeScheduler\Scheduler\Scheduler&\Mockery\MockInterface $scheduler */
        $scheduler = Mockery::mock(Scheduler::class, [new Container()])->makePartial();
        $this->scheduler = $scheduler;
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
            $event = new Event(new TestAppCommand(), ['somearg', '--myoption=someoption']);
            $collection = new Collection([$event]);

            /** @var \Mockery\ExpectationInterface $expectation */
            $expectation = $this->scheduler->shouldReceive('allEvents');
            $expectation->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:view');

        $this->assertExitSuccess();
        $this->assertOutputContains('* * * * * | TestApp\Command\TestAppCommand [somearg --myoption=someoption]');
    }

    public function testRunScheduleViewNoEvents(): void
    {
        $this->mockService(Scheduler::class, function () {
            $collection = new Collection([]);

            /** @var \Mockery\ExpectationInterface $expectation */
            $expectation = $this->scheduler->shouldReceive('allEvents');
            $expectation->andReturn($collection);

            return $this->scheduler;
        });
        $this->exec('schedule:view');

        $this->assertExitSuccess();
        $this->assertOutputContains('No commands are configured.');
    }
}
