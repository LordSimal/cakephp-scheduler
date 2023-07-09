<?php
declare(strict_types=1);

namespace TestPlugin\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class TestPluginCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->info('Test Plugin Command executed');
    }
}