<?php

namespace Phpactor\AmpFsWatcher\Tests\Watcher;

use Amp\Delayed;
use Amp\Loop;
use Amp\Loop\DriverFactory;
use Closure;
use Generator;
use Phpactor\AmpFsWatch\ModifiedFile;
use Phpactor\AmpFsWatch\Watcher;
use Phpactor\AmpFsWatch\Watcher\Inotify\InotifyWatcher;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Phpactor\AmpFsWatcher\Tests\IntegrationTestCase;

abstract class WatcherTestCase extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->workspace()->reset();
    }

    protected function createLogger(): LoggerInterface
    {
        return new class extends AbstractLogger {
            public function log($level, $message, array $context = [])
            {
                fwrite(STDERR, sprintf('[%s] [%s] %s', microtime(), $level, $message)."\n");
            }
        };
    }

    protected function runLoop(array $paths, Closure $plan): array
    {
        $watcher = $this->createWatcher();
        $modifications = [];
        $watcher->monitor($paths, function (ModifiedFile $modification) use (&$modifications) {
            $modifications[] = $modification;
        });
        
        Loop::run(function () use ($plan) {
            $generator = $plan();
            yield from $generator;
            Loop::stop();
        });

        return $modifications;
    }

    abstract protected function createWatcher(): Watcher;
}
