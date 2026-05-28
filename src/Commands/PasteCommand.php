<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PasteCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $delim = "\t";
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-d' && isset($argv[$i+1])) { $delim = $argv[++$i]; }
            elseif (preg_match('/^-d(.+)$/', $a, $m)) { $delim = $m[1]; }
            else $files[] = $a;
        }
        if (count($files) === 0) {
            // paste from stdin: just echo stdin
            while (!feof(STDIN)) echo rtrim(fgets(STDIN), "\r\n") . PHP_EOL;
            return 0;
        }
        $columns = [];
        $max = 0;
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "paste: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) $lines = [];
            $columns[] = $lines;
            $max = max($max, count($lines));
        }
        for ($i=0;$i<$max;$i++) {
            $cells = [];
            foreach ($columns as $col) $cells[] = $col[$i] ?? '';
            echo implode($delim, $cells) . PHP_EOL;
        }
        return 0;
    }
}
