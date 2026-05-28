<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class NprocCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Pure-PHP: try to detect CPU count from /proc/cpuinfo
        if (is_readable('/proc/cpuinfo')) {
            $contents = @file_get_contents('/proc/cpuinfo');
            if ($contents !== false) {
                $c = preg_match_all('/^processor\s*:\s*[0-9]+/m', $contents);
                if ($c) { echo $c . PHP_EOL; return 0; }
            }
        }
        // macOS or other platforms: no reliable pure-PHP method available here
        // Fall back to 1
        // Generic fallback
        echo 1 . PHP_EOL;
        return 0;
    }
}
