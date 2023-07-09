<?php
declare(strict_types=1);

namespace CakeScheduler\Scheduler\Traits;

use Cake\Chronos\Chronos;
use CakeScheduler\Scheduler\Event;
use InvalidArgumentException;

trait FrequenciesTrait
{
    protected string $expression = '* * * * *';

    /**
     * @param string $expression The complete cron valid string
     * @return \CakeScheduler\Scheduler\Event
     */
    public function cron(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function everyMinute(): self
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * @param int $interval The interval between 0 and 59
     * @return \CakeScheduler\Scheduler\Event
     */
    public function everyXMinutes(int $interval): self
    {
        if ($interval < 0 || $interval > 59) {
            throw new InvalidArgumentException(sprintf('Given interval of `%s` is not valid', $interval));
        }

        return $this->spliceIntoPosition(1, '*/' . $interval);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function hourly(): self
    {
        return $this->spliceIntoPosition(1, 0);
    }

    /**
     * @param mixed $offset The amount of minutes which should pass per hour till the command is executed
     * @return \CakeScheduler\Scheduler\Event
     */
    public function hourlyAt($offset): self
    {
        $offset = is_array($offset) ? implode(',', $offset) : $offset;

        return $this->spliceIntoPosition(1, $offset);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function everyOddHour(): self
    {
        return $this->spliceIntoPosition(1, 0)->spliceIntoPosition(2, '1-23/2');
    }

    /**
     * @param int $interval The amount of hours between each event execution
     * @return \CakeScheduler\Scheduler\Event
     */
    public function everyXHours(int $interval): self
    {
        if ($interval < 2 || $interval > 23) {
            throw new InvalidArgumentException(sprintf('Given interval of `%s` is not valid', $interval));
        }

        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, '*/' . $interval);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function daily(): self
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0);
    }

    /**
     * @param string $time The time in 12:34 format
     * @return \CakeScheduler\Scheduler\Event
     */
    public function dailyAt(string $time): self
    {
        $segments = explode(':', $time);

        return $this->spliceIntoPosition(2, (int)$segments[0])
            ->spliceIntoPosition(1, count($segments) === 2 ? (int)$segments[1] : '0');
    }

    /**
     * @param int $first First hour number
     * @param int $second Second hour number
     * @param mixed $minuteOffset The amount of minutes
     * @return \CakeScheduler\Scheduler\Event
     */
    public function twiceDailyAt(int $first = 1, int $second = 13, $minuteOffset = 0): self
    {
        $hours = $first . ',' . $second;

        return $this->spliceIntoPosition(1, $minuteOffset)
            ->spliceIntoPosition(2, $hours);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function weekdays(): self
    {
        return $this->days(Event::MONDAY . '-' . Event::FRIDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function weekends(): self
    {
        return $this->days(Event::SATURDAY . ',' . Event::SUNDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function mondays(): self
    {
        return $this->days(Event::MONDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function tuesdays(): self
    {
        return $this->days(Event::TUESDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function wednesdays(): self
    {
        return $this->days(Event::WEDNESDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function thursdays(): self
    {
        return $this->days(Event::THURSDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function fridays(): self
    {
        return $this->days(Event::FRIDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function saturdays(): self
    {
        return $this->days(Event::SATURDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function sundays(): self
    {
        return $this->days(Event::SUNDAY);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function weekly(): self
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(5, 0);
    }

    /**
     * @param int $dayOfWeek The day of the week between 0-6
     * @param string $time The hour and minute values
     * @return \CakeScheduler\Scheduler\Event
     */
    public function weeklyOn(int $dayOfWeek, string $time = '0:0'): self
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function monthly(): self
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1);
    }

    /**
     * @param int $dayOfMonth The day of the week between 1-31
     * @param string $time The hour and minute values
     * @return \CakeScheduler\Scheduler\Event
     */
    public function monthlyOn(int $dayOfMonth = 1, string $time = '0:0'): self
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth);
    }

    /**
     * @param int $first First day number between 1-31
     * @param int $second Second day number between 1-31
     * @param string $timeOffset The amount of minutes and hours
     * @return \CakeScheduler\Scheduler\Event
     */
    public function twiceMonthly(int $first = 1, int $second = 16, string $timeOffset = '0:0'): self
    {
        $daysOfMonth = $first . ',' . $second;

        $this->dailyAt($timeOffset);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    /**
     * @param string $time The amount of minutes and hours
     * @return \CakeScheduler\Scheduler\Event
     */
    public function lastDayOfMonth(string $time = '0:0'): self
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, Chronos::now()->endOfMonth()->day);
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function quarterly(): self
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * @param int $dayOfQuarter The day in the quarter
     * @param string $time The amount of minutes and hours
     * @return \CakeScheduler\Scheduler\Event
     */
    public function quarterlyOn(int $dayOfQuarter = 1, string $time = '0:0'): self
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfQuarter)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * @return \CakeScheduler\Scheduler\Event
     */
    public function yearly(): self
    {
        return $this->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, 1);
    }

    /**
     * @param int $month The month
     * @param string $dayOfMonth The day of the month or '*' for all
     * @param string $time The amount of minutes and hours
     * @return \CakeScheduler\Scheduler\Event
     */
    public function yearlyOn(int $month = 1, string $dayOfMonth = '1', string $time = '0:0'): self
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $dayOfMonth)
            ->spliceIntoPosition(4, $month);
    }

    /**
     * @param int|string|array $days The day number or days string combination or an array of day numbers
     * @return \CakeScheduler\Scheduler\Event
     */
    public function days($days): self
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    /**
     * @param int $position The position inside the cron entry
     * @param mixed $value The value at that position in the cron entry
     * @return \CakeScheduler\Scheduler\Event
     */
    protected function spliceIntoPosition(int $position, $value): self
    {
        $segments = preg_split("/\s+/", $this->expression);

        $segments[$position - 1] = $value;

        return $this->cron(implode(' ', $segments));
    }
}
