<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SortCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $reverse = false;
        $unique = false;
        $files = [];
        foreach ($argv as $a) {
            if ($a === '-r') $reverse = true;
            elseif ($a === '-u') $unique = true;
            else $files[] = $a;
        }
        $lines = [];
        if (count($files) === 0) {
            while (!feof(STDIN)) {
                $lines[] = rtrim(fgets(STDIN), "\r\n");
            }
        } else {
            foreach ($files as $f) {
                if (!is_readable($f)) {
                    fwrite(STDERR, "sort: cannot read '$f'\n");
                    return 1;
                }
                $content = file($f, FILE_IGNORE_NEW_LINES);
                if ($content !== false) $lines = array_merge($lines, $content);
            }
        }
        sort($lines, SORT_STRING);
        if ($unique) {
            $lines = array_values(array_unique($lines));
        }
        if ($reverse) $lines = array_reverse($lines);
        foreach ($lines as $l) echo $l . PHP_EOL;
        return 0;
    }
}
