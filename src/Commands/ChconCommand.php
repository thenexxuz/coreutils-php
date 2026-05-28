<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ChconCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "chcon: missing operand\n"); return 1; }
        // SELinux context manipulation is not available in pure PHP portably.
        foreach ($argv as $f) {
            if (!file_exists($f)) { fwrite(STDERR, "chcon: cannot access '$f'\n"); }
            else { fwrite(STDERR, "chcon: operation not supported in pure-PHP implementation\n"); }
        }
        return 1;
    }
}
