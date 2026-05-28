<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DateCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { echo date('Y-m-d H:i:s O') . PHP_EOL; return 0; }
        // support +FORMAT
        if (count($argv) === 1 && strpos($argv[0], '+') === 0) {
            $fmt = substr($argv[0], 1);
            // naive: pass through to date()
            echo date($fmt) . PHP_EOL;
            return 0;
        }
        fwrite(STDERR, "date: unsupported options\n");
        return 1;
    }
}
