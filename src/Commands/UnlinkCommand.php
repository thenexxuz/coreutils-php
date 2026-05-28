<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class UnlinkCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            fwrite(STDERR, "unlink: missing operand\n");
            return 1;
        }
        $exit = 0;
        foreach ($argv as $f) {
            if (!file_exists($f)) { fwrite(STDERR, "unlink: $f: No such file or directory\n"); $exit = 1; continue; }
            if (!unlink($f)) { fwrite(STDERR, "unlink: failed to remove '$f'\n"); $exit = 1; }
        }
        return $exit;
    }
}
