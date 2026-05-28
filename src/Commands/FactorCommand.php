<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class FactorCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "factor: missing operand\n"); return 1; }
        foreach ($argv as $v) {
            $n = (int)$v;
            if ($n < 2) { echo $n . ": " . $n . PHP_EOL; continue; }
            $orig = $n; $factors = [];
            while ($n % 2 === 0) { $factors[] = 2; $n /= 2; }
            for ($p = 3; $p * $p <= $n; $p += 2) {
                while ($n % $p === 0) { $factors[] = $p; $n = intdiv($n, $p); }
            }
            if ($n > 1) $factors[] = $n;
            echo $orig . ": " . implode(' ', $factors) . PHP_EOL;
        }
        return 0;
    }
}
