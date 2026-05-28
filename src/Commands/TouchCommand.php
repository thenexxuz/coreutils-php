<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TouchCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $times = null; // not supporting -t for now
        $files = [];
        foreach ($argv as $a) {
            if ($a === '-t') { /* ignore for now */ }
            else $files[] = $a;
        }
        if (count($files) === 0) {
            fwrite(STDERR, "touch: missing file operand\n");
            return 1;
        }
        foreach ($files as $f) {
            if (@touch($f)) continue;
            // try creating
            $h = @fopen($f, 'c');
            if ($h === false) { fwrite(STDERR, "touch: cannot touch '$f'\n"); return 1; }
            fclose($h);
        }
        return 0;
    }
}
