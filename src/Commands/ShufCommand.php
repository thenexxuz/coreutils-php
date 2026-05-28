<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ShufCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $count = null;
        $files = [];
        foreach ($argv as $a) {
            if ($a === '-n' && isset($argv[1])) { $count = (int)array_shift($argv); continue; }
            if (preg_match('/^-n(\d+)$/', $a, $m)) { $count = (int)$m[1]; }
            else $files[] = $a;
        }
        $lines = [];
        if (count($files) === 0) {
            while (!feof(STDIN)) $lines[] = rtrim(fgets(STDIN), "\r\n");
        } else {
            foreach ($files as $f) {
                if (!is_readable($f)) { fwrite(STDERR, "shuf: cannot open '$f'\n"); return 1; }
                $content = file($f, FILE_IGNORE_NEW_LINES);
                if ($content !== false) $lines = array_merge($lines, $content);
            }
        }
        if (empty($lines)) return 0;
        shuffle($lines);
        if ($count !== null) $lines = array_slice($lines, 0, $count);
        foreach ($lines as $l) echo $l . PHP_EOL;
        return 0;
    }
}
