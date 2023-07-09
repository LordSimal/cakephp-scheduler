<?php
declare(strict_types=1);

namespace TestApp\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class TestAppCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->info('Test App Command executed');
    }
}