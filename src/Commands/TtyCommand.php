<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TtyCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // prefer stream_isatty if available
        if (function_exists('stream_isatty') && stream_isatty(STDIN)) {
            // Best-effort: report /dev/tty when available
            if (is_readable('/dev/tty')) { echo '/dev/tty' . PHP_EOL; return 0; }
            echo 'not a tty' . PHP_EOL; return 1;
        }
        // No tty available
        fwrite(STDERR, "not a tty\n"); return 1;
    }
}
