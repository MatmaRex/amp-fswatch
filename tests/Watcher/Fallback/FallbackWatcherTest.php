<?php

namespace Phpactor\AmpFsWatcher\Tests\Watcher\Fallback;

use PHPUnit\Framework\TestCase;
use Phpactor\AmpFsWatch\Watcher;
use Phpactor\AmpFsWatch\Watcher\Fallback\FallbackWatcher;
use Phpactor\AmpFsWatch\Watcher\Null\NullWatcher;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class FallbackWatcherTest extends TestCase
{
    /**
     * @var ObjectProphecy|LoggerInterface
     */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->watcher1 = $this->prophesize(Watcher::class);
        $this->watcher2 = $this->prophesize(Watcher::class);
    }

    public function testUsesFirstSupportedWatcher()
    {
        $this->watcher1->isSupported()->willReturn(false);

        $callback = function () {
        };
        $paths = ['path1'];

        $nullWatcher = new NullWatcher();

        $process = $this->createWatcher([
            $this->watcher1->reveal(),
            $nullWatcher
        ])->watch($paths, $callback);

        self::assertSame($nullWatcher, $process);
    }

    public function testReturnsNullWatcherAndLogsWarningIfNoSupportedWatchers()
    {
        $this->watcher1->isSupported()->willReturn(false);
        $this->watcher2->isSupported()->willReturn(false);

        $callback = function () {
        };
        $paths = ['path1'];

        $process = $this->createWatcher([
            $this->watcher1->reveal(),
            $this->watcher2->reveal(),
        ])->watch($paths, $callback);

        $this->logger->warning(Argument::containingString('No supported watchers'))->shouldHaveBeenCalled();

        self::assertInstanceOf(NullWatcher::class, $process);
    }

    public function testIsAlwaysSupported()
    {
        $watcher = $this->createWatcher([]);
        self::assertTrue($watcher->isSupported());
    }

    private function createWatcher(array $watchers): Watcher
    {
        return new FallbackWatcher(
            $watchers,
            $this->logger->reveal()
        );
    }
}