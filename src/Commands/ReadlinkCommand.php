<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ReadlinkCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $canonical = false;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-f') { $canonical = true; }
            else $paths[] = $a;
        }
        if (count($paths) === 0) {
            fwrite(STDERR, "readlink: missing operand\n");
            return 1;
        }
        foreach ($paths as $p) {
            if ($canonical) {
                $rp = realpath($p);
                if ($rp === false) { fwrite(STDERR, "readlink: $p: No such file or directory\n"); continue; }
                echo $rp . PHP_EOL;
            } else {
                $t = readlink($p);
                if ($t === false) { fwrite(STDERR, "readlink: $p: Invalid argument\n"); continue; }
                echo $t . PHP_EOL;
            }
        }
        return 0;
    }
}
