<?php

namespace Jundayw\Proxy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Jundayw\Proxy\Contracts\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'proxy:clear-compiled', aliases: ['proxy:clear'])]
class ClearCompiledCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:clear-compiled';

    /**
     * The console command name aliases.
     *
     * @var string[]
     */
    protected $aliases = ['proxy:clear'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the compiled class file';

    public function __construct(
        protected Configuration $configuration,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        foreach ([
                     $this->configuration->getNamespacePath(),
                     $this->configuration->getNamespacePath(false),
                 ] as $alias) {
            $this->clear($alias);
        }

        $this->info('Compiled class files cleared successfully.');

        return Command::SUCCESS;
    }

    protected function clear(string $path): bool
    {
        if (!File::isDirectory($path)) {
            return false;
        }

        $files = File::files($path);
        foreach ($files as $file) {
            if ($file === '.gitignore') {
                continue;
            }
            File::delete($file);
        }

        $dirs = File::directories($path);
        foreach ($dirs as $dir) {
            File::deleteDirectory($dir);
        }

        return true;
    }
}
