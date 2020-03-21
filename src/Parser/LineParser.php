<?php

namespace Phpactor\AmpFsWatch\Parser;

use Amp\Process\ProcessInputStream;

class LineParser
{
    /**
     * @var string
     */
    private $buffer;

    public function stream(ProcessInputStream $stream, callable $callback): void
    {
        \Amp\asyncCall(function () use ($stream, $callback) {
            while (null !== $chunk = yield $stream->read()) {
                foreach (str_split($chunk) as $char) {
                    if ($char !== "\n") {
                        $this->buffer .= $char;
                        continue;
                    }

                    $line = $this->buffer;
                    $this->buffer = '';
                    $callback($line);
                }
            }
        });
    }
}