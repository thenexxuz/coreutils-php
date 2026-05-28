<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class UniqCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $count = false;
        $files = [];
        foreach ($argv as $a) {
            if ($a === '-c') $count = true;
            else $files[] = $a;
        }
        $input = [];
        if (count($files) === 0) {
            while (!feof(STDIN)) $input[] = rtrim(fgets(STDIN), "\r\n");
        } else {
            foreach ($files as $f) {
                if (!is_readable($f)) {
                    fwrite(STDERR, "uniq: cannot read '$f'\n");
                    return 1;
                }
                $lines = file($f, FILE_IGNORE_NEW_LINES);
                if ($lines !== false) $input = array_merge($input, $lines);
            }
        }
        $prev = null;
        $cnt = 0;
        foreach ($input as $line) {
            if ($prev === null) { $prev = $line; $cnt = 1; continue; }
            if ($line === $prev) {
                $cnt++;
            } else {
                if ($count) echo sprintf('%4d %s', $cnt, $prev) . PHP_EOL;
                else echo $prev . PHP_EOL;
                $prev = $line; $cnt = 1;
            }
        }
        if ($prev !== null) {
            if ($count) echo sprintf('%4d %s', $cnt, $prev) . PHP_EOL;
            else echo $prev . PHP_EOL;
        }
        return 0;
    }
}
