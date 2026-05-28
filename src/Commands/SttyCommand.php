<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SttyCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Minimal pure-PHP stty: only report whether stdin is a TTY
        if (function_exists('stream_isatty') && stream_isatty(STDIN)) {
            echo "speed 38400 baud; rows 0; columns 0;\n";
            return 0;
        }
        fwrite(STDERR, "stty: not a tty or not supported\n");
        return 1;
    }
}
