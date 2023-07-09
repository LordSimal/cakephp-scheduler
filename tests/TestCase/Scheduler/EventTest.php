<?php
declare(strict_types=1);

namespace CakeScheduler\Test\TestCase\Scheduler;

use Cake\Chronos\Chronos;
use Cake\Console\Command\HelpCommand;
use Cake\TestSuite\TestCase;
use CakeScheduler\Scheduler\Event;

class EventTest extends TestCase
{
    protected Event $event;

    protected function setUp(): void
    {
        $this->event = new Event(new HelpCommand());
    }

    public function testIsDue(): void
    {
        $this->assertTrue($this->event->isDue());
        Chronos::setTestNow('2020-10-10 10:10:10');
        $this->assertFalse($this->event->everyXHours(20)->isDue());
        Chronos::setTestNow('now');
    }

    public function testEveryMinute()
    {
        $this->assertSame('* * * * *', $this->event->getExpression());
        $this->assertSame('* * * * *', $this->event->everyMinute()->getExpression());
    }

    public function testEveryXMinutes()
    {
        $this->assertSame('*/2 * * * *', $this->event->everyXMinutes(2)->getExpression());
        $this->expectException(\InvalidArgumentException::class);
        $this->assertSame('*/2 * * * *', $this->event->everyXMinutes(-10)->getExpression());
    }

    public function testDaily()
    {
        $this->assertSame('0 0 * * *', $this->event->daily()->getExpression());
    }

    public function testDailyAt()
    {
        $this->assertSame('8 13 * * *', $this->event->dailyAt('13:08')->getExpression());
    }

    public function testTwiceDailyAt()
    {
        $this->assertSame('0 3,15 * * *', $this->event->twiceDailyAt(3, 15)->getExpression());
        $this->assertSame('5 3,15 * * *', $this->event->twiceDailyAt(3, 15, 5)->getExpression());
    }

    public function testWeekly()
    {
        $this->assertSame('0 0 * * 0', $this->event->weekly()->getExpression());
    }

    public function testWeeklyOn()
    {
        $this->assertSame('0 8 * * 1', $this->event->weeklyOn(Event::MONDAY, '8:00')->getExpression());
    }

    public function testOverrideWithHourly()
    {
        $this->assertSame('0 * * * *', $this->event->everyXMinutes(5)->hourly()->getExpression());
        $this->assertSame('37 * * * *', $this->event->hourlyAt(37)->getExpression());
        $this->assertSame('15,30,45 * * * *', $this->event->hourlyAt([15, 30, 45])->getExpression());
    }

    public function testHourly()
    {
        $this->assertSame('0 1-23/2 * * *', $this->event->everyOddHour()->getExpression());
        $this->assertSame('0 */2 * * *', $this->event->everyXHours(2)->getExpression());
        $this->expectException(\InvalidArgumentException::class);
        $this->assertSame('*/2 * * * *', $this->event->everyXHours(-10)->getExpression());
    }

    public function testMonthly()
    {
        $this->assertSame('0 0 1 * *', $this->event->monthly()->getExpression());
    }

    public function testMonthlyOn()
    {
        $this->assertSame('0 15 4 * *', $this->event->monthlyOn(4, '15:00')->getExpression());
    }

    public function testLastDayOfMonth()
    {
        Chronos::setTestNow('2020-10-10 10:10:10');
        $this->assertSame('0 0 31 * *', $this->event->lastDayOfMonth()->getExpression());
        Chronos::setTestNow('now');
    }

    public function testTwiceMonthly()
    {
        $this->assertSame('0 0 1,16 * *', $this->event->twiceMonthly(1, 16)->getExpression());
    }

    public function testTwiceMonthlyAtTime()
    {
        $this->assertSame('30 1 1,16 * *', $this->event->twiceMonthly(1, 16, '1:30')->getExpression());
    }

    public function testMonthlyOnWithMinutes()
    {
        $this->assertSame('15 15 4 * *', $this->event->monthlyOn(4, '15:15')->getExpression());
    }

    public function testWeekdaysDaily()
    {
        $this->assertSame('0 0 * * 1-5', $this->event->weekdays()->daily()->getExpression());
    }

    public function testWeekdaysHourly()
    {
        $this->assertSame('0 * * * 1-5', $this->event->weekdays()->hourly()->getExpression());
    }

    public function testWeekdays()
    {
        $this->assertSame('* * * * 1-5', $this->event->weekdays()->getExpression());
    }

    public function testWeekends()
    {
        $this->assertSame('* * * * 6,0', $this->event->weekends()->getExpression());
    }

    public function testSundays()
    {
        $this->assertSame('* * * * 0', $this->event->sundays()->getExpression());
    }

    public function testMondays()
    {
        $this->assertSame('* * * * 1', $this->event->mondays()->getExpression());
    }

    public function testTuesdays()
    {
        $this->assertSame('* * * * 2', $this->event->tuesdays()->getExpression());
    }

    public function testWednesdays()
    {
        $this->assertSame('* * * * 3', $this->event->wednesdays()->getExpression());
    }

    public function testThursdays()
    {
        $this->assertSame('* * * * 4', $this->event->thursdays()->getExpression());
    }

    public function testFridays()
    {
        $this->assertSame('* * * * 5', $this->event->fridays()->getExpression());
    }

    public function testSaturdays()
    {
        $this->assertSame('* * * * 6', $this->event->saturdays()->getExpression());
    }

    public function testDays()
    {
        $this->assertSame('* * * * 1', $this->event->days(Event::MONDAY)->getExpression());
        $this->assertSame('* * * * 1,4', $this->event->days([Event::MONDAY, Event::THURSDAY])->getExpression());
    }

    public function testQuarterly()
    {
        $this->assertSame('0 0 1 1-12/3 *', $this->event->quarterly()->getExpression());
    }

    public function testQuarterlyOn()
    {
        $this->assertSame('0 0 2 1-12/3 *', $this->event->quarterlyOn(2)->getExpression());
    }

    public function testYearly()
    {
        $this->assertSame('0 0 1 1 *', $this->event->yearly()->getExpression());
    }

    public function testYearlyOn()
    {
        $this->assertSame('8 15 5 4 *', $this->event->yearlyOn(4, '5', '15:08')->getExpression());
    }

    public function testYearlyOnMondaysOnly()
    {
        $this->assertSame('1 9 * 7 1', $this->event->mondays()->yearlyOn(7, '*', '09:01')->getExpression());
    }

    public function testYearlyOnTuesdaysAndDayOfMonth20()
    {
        $this->assertSame('1 9 20 7 2', $this->event->tuesdays()->yearlyOn(7, '20', '09:01')->getExpression());
    }
}
