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

    public function execute()
    {
        $this->io->info('Test App Command executed');
        $arg = $this->args->getArgument('myarg');
        if ($arg) {
            $this->io->info('with arg ' . $arg);
        }
        $option = $this->args->getOption('myoption');
        if ($option) {
            $this->io->info('with option ' . $option);
        }
    }
}
