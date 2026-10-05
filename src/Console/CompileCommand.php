<?php

namespace Jundayw\Proxy\Console;

use Illuminate\Console\Command;
use Jundayw\Proxy\Contracts\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'proxy:compiled')]
class CompileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:compiled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile proxy class files';

    public function __construct(
        protected Configuration $configuration,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->callSilently('proxy:clear');

        return Command::SUCCESS;
    }

}
