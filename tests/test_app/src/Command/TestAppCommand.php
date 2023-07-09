<?php
declare(strict_types=1);

namespace TestApp\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class TestAppCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('myarg', [
            'help' => 'Some help text',
            'required' => false
        ])
            ->addOption('myoption');

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->info('Test App Command executed');
        $arg = $args->getArgument('myarg');
        if ($arg) {
            $io->info('with arg ' . $arg);
        }
        $option = $args->getOption('myoption');
        if ($option) {
            $io->info('with option ' . $option);
        }
    }
}