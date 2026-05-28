<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SleepCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "sleep: missing operand\n"); return 1; }
        $t = (float)$argv[0];
        if ($t <= 0) return 0;
        usleep((int)($t * 1000000));
        return 0;
    }
}
