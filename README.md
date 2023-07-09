# CakePHP Scheduler Plugin

[![Latest Stable Version](http://poser.pugx.org/lordsimal/cakephp-scheduler/v)](https://packagist.org/packages/lordsimal/cakephp-scheduler) [![Total Downloads](http://poser.pugx.org/lordsimal/cakephp-scheduler/downloads)](https://packagist.org/packages/lordsimal/cakephp-scheduler) [![Latest Unstable Version](http://poser.pugx.org/lordsimal/cakephp-scheduler/v/unstable)](https://packagist.org/packages/lordsimal/cakephp-scheduler) [![License](http://poser.pugx.org/lordsimal/cakephp-scheduler/license)](https://packagist.org/packages/lordsimal/cakephp-scheduler) [![PHP Version Require](http://poser.pugx.org/lordsimal/cakephp-scheduler/require/php)](https://packagist.org/packages/lordsimal/cakephp-scheduler)
[![codecov](https://codecov.io/github/LordSimal/cakephp-scheduler/branch/main/graph/badge.svg?token=XFRMhXp6S9)](https://codecov.io/github/LordSimal/cakephp-scheduler)

## What can this plugin do?

This tool allows you to move all your cron jobs from being server configured via `crontab` 
to being app controlled in your CakePHP application (and plugins).

## Requirements
- PHP 7.4+ / PHP 8.0+
- CakePHP 4.4+

## Installation
```
composer require lordsimal/cakephp-scheduler
```

### Loading plugin
In Application.php

```php
public function bootstrap()
{
    parent::bootstrap();

    $this->addPlugin(\CakeScheduler\CakeSchedulerPlugin::class);
}
```

Or use the cake CLI.
```
bin/cake plugin load CakeScheduler
```

## Usage

### Defining a schedule

Either your app or your plugin need to implement the `CakeSchedulerInterface`
which will add the `schedule(Scheduler &$scheduler)` method.

```php
<?php
 
namespace App;

use App\Command\MyAppCommand;
use App\Command\OtherAppCommand;
use Cake\Http\BaseApplication;
use CakeScheduler\CakeSchedulerInterface;
use CakeScheduler\Scheduler\Scheduler;

class Application extends BaseApplication implements CakeSchedulerInterface
{
    public function schedule(Scheduler &$scheduler): void
    {
        $scheduler->execute(MyAppCommand::class)->daily();
        $scheduler->execute(OtherAppCommand::class, ['somearg', '--myoption=someoption'])->daily();
    }
}
```

with the `->execute()` method you define which Command should be executed.

Each `->execute()` method will return a `\CakeScheduler\Scheduler\Event` instance which 
is used to tell the scheduler when the command should be executed.

### Available frequencies

| Method                                      | Run the command                             |
|---------------------------------------------|---------------------------------------------|
| `->cron('* * * * *');`                      | with the exact cron config                  |
| `->everyMinute();`                          | every minute                                |
| `->everyXMinutes(5);`                       | every 5 minutes                             |
| `->hourly();`                               | every hour                                  |
| `->everyOddHour();`                         | every odd hour                              |
| `->everyXHours(4);`                         | every 4 hours                               |
| `->hourlyAt(37);`                           | every hour at 37 minutes                    |
| `->hourlyAt([15, 30, 45]);`                 | every hour at 15, 30 and 45 minutes         |
| `->daily();`                                | every day at midnight                       |
| `->dailyAt('13:08');`                       | every day at 13:08                          |
| `->twiceDailyAt(3, 15, 5);`                 | every day at 03:05 and 15:05                |
| `->weekly();`                               | every sunday at 00:00                       |
| `->weeklyOn(Event::MONDAY, '8:00');`        | every week on monday at 08:00               |
| `->monthly();`                              | on the first day of the month at midnight   |
| `->monthlyOn(4, '15:00');`                  | on the 4th of the month at 15:00            |
| `->twiceMonthly(1, 16, '13:00');`           | on the 1st and 16th of the month at 13:00   |
| `->lastDayOfMonth('12:00');`                | on the last day of the month at 12:00       |
| `->weekdays();`                             | from monday to friday                       |
| `->weekends();`                             | on saturday and sunday                      |
| `->mondays();`                              | on every monday                             |
| `->tuesdays();`                             | on every tuesday                            |
| `->wednesdays();`                           | on every wednesday                          |
| `->thursdays();`                            | on every thursday                           |
| `->fridays();`                              | on every friday                             |
| `->saturdays();`                            | on every saturday                           |
| `->sundays();`                              | on every sunday                             |
| `->days([Event::MONDAY, Event::THURSDAY]);` | on every monday and thursday                |
| `->quarterly();`                            | on the first day of the quarter at midnight |
| `->quarterlyOn(5, '08:00');`                | on the 5th day of the quarter at 08:00      |
| `->yearly();`                               | on the first day of the year at midnight    |
| `->yearlyOn(4, '5', '15:00');`              | on the 5th of april at 15:00 every year     |

Also see the [EventTest](https://github.com/LordSimal/cakephp-scheduler/blob/main/tests/TestCase/Scheduler/EventTest.php) 
for all the available options

### List all scheduled events

```
bin/cake schedule:view
```

### Running the Scheduler

You still need the following entry in your `crontab`

```
* * * * * cd /path-to-your-project && bin/cake schedule:run >> /dev/null 2>&1
```

## Credit where credit is due
This plugin is heavily inspired by the [Laravel Task Scheduling Feature](https://laravel.com/docs/10.x/scheduling)

## License
The plugin is available as open source under the terms of the [MIT License](https://github.com/lordsimal/cakephp-scheduler/blob/main/LICENSE).