<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DircolorsCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            // print nothing by default (safe no-op)
            return 0;
        }
        foreach ($argv as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "dircolors: cannot read '$f'\n"); return 1; }
            echo file_get_contents($f);
        }
        return 0;
    }
}
