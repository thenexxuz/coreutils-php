<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SeqCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $start = 1; $step = 1; $end = null;
        if (count($argv) === 1) { $end = (int)$argv[0]; }
        elseif (count($argv) === 2) { $start = (int)$argv[0]; $end = (int)$argv[1]; }
        elseif (count($argv) >= 3) { $start = (int)$argv[0]; $step = (int)$argv[1]; $end = (int)$argv[2]; }
        if ($end === null) { fwrite(STDERR, "seq: missing operand\n"); return 1; }
        if ($step === 0) $step = 1;
        if ($step > 0) {
            for ($i = $start; $i <= $end; $i += $step) echo $i . PHP_EOL;
        } else {
            for ($i = $start; $i >= $end; $i += $step) echo $i . PHP_EOL;
        }
        return 0;
    }
}
