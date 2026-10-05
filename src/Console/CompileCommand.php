<?php

namespace Jundayw\Proxy\Console;

use Annotation\Scannable\Attributes\ScanNamespace;
use Annotation\Scannable\Attributes\ScanPath;
use App\Models\Model;
use Illuminate\Console\Command;
use Jundayw\Proxy\Contracts\Configuration;
use Jundayw\Proxy\Contracts\ProxyManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;

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
        protected ProxyManager $proxyManager,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->callSilently('proxy:clear');

        $path      = new ScanPath($this->configuration->getDirectories());
        $namespace = new ScanNamespace($this->configuration->getClasses());

        $items = array_merge($namespace->getNamespace(), $path->getNamespace());

        $this->withProgressBar($items, function (string $item, ProgressBar $bar) {
            usleep(100000);
            $bar->setFormat("%message% \r\n%current%/%max% [%bar%] %percent:3s%%");
            $bar->setMessage("正在处理: {$item} => {$this->proxyManager->create($item)}");
        });

        $this->newLine();
        $this->info('Compiling class files');

        return Command::SUCCESS;
    }

}
