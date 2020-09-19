<?php

namespace Phpactor\AmpFsWatch;

class WatcherConfig
{
    /**
     * @var array<string>
     */
    private $paths;

    /**
     * @var int
     */
    private $pollInterval;

    /**
     * @var array<string>
     */
    private $filePatterns;

    /**
     * @var string|null
     */
    private $lastUpdateReferenceFile;

    /**
     * @param array<string> $paths
     * @param array<string> $filePatterns
     */
    public function __construct(array $paths, int $pollInterval = 1000, array $filePatterns = [], ?string $lastUpdateReferenceFile = null)
    {
        $this->paths = $paths;
        $this->pollInterval = $pollInterval;
        $this->filePatterns = $filePatterns;
        $this->lastUpdateReferenceFile = $lastUpdateReferenceFile;
    }

    public function withPath(string $path): self
    {
        $new = clone $this;
        $new->paths[] = $path;
        return $new;
    }

    public function withPollInterval(int $pollInterval): self
    {
        $new = clone $this;
        $new->pollInterval = $pollInterval;
        return $new;
    }

    public function withLastUpdateReferenceFile(string $lastUpdateReferenceFile): self
    {
        $new = clone $this;
        $this->lastUpdateReferenceFile = $lastUpdateReferenceFile;
        return $new;
    }

    /**
     * @return array<string>
     */
    public function paths(): array
    {
        return $this->paths;
    }

    /**
     * Used by all polling implementations (e.g. find)
     */
    public function pollInterval(): int
    {
        return $this->pollInterval;
    }

    /**
     * @return array<string>
     */
    public function filePatterns(): array
    {
        return $this->filePatterns;
    }

    /**
     * Used by f.e. `find` watcher - any files with a change time greater than
     * this file will be returned.
     */
    public function lastUpdateReferenceFile(): ?string
    {
        return $this->lastUpdateReferenceFile;
    }
}
