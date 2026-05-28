<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TeeCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $append = false;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-a') { $append = true; }
            else $files[] = $a;
        }
        $modes = $append ? 'ab' : 'wb';
        $handles = [];
        foreach ($files as $f) {
            $h = @fopen($f, $modes);
            if ($h === false) { fwrite(STDERR, "tee: cannot open '$f'\n"); return 1; }
            $handles[] = $h;
        }
        while (!feof(STDIN)) {
            $line = fgets(STDIN);
            if ($line === false) break;
            echo $line;
            foreach ($handles as $h) fwrite($h, $line);
        }
        foreach ($handles as $h) fclose($h);
        return 0;
    }
}
