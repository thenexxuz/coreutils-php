<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TailCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $n = 10;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-n' && isset($argv[$i+1])) { $n = (int)$argv[++$i]; }
            elseif (preg_match('/^-n(\d+)$/', $a, $m)) { $n = (int)$m[1]; }
            elseif (preg_match('/^-(\d+)$/', $a, $m)) { $n = (int)$m[1]; }
            else $files[] = $a;
        }
        if (count($files) === 0) {
            $lines = [];
            while (!feof(STDIN)) $lines[] = rtrim(fgets(STDIN), "\r\n");
            $last = array_slice($lines, -$n);
            foreach ($last as $l) echo $l . PHP_EOL;
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "tail: cannot open '$f' for reading\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            $last = array_slice($lines, -$n);
            foreach ($last as $l) echo $l . PHP_EOL;
        }
        return 0;
    }
}
