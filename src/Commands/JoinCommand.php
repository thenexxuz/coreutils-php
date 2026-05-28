<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class JoinCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Simple line-wise join: if two or more files given, join corresponding lines with a space.
        $files = $argv;
        if (count($files) === 0) {
            // read all stdin and echo
            while (!feof(STDIN)) echo rtrim(fgets(STDIN), "\r\n") . PHP_EOL;
            return 0;
        }
        $cols = [];
        $max = 0;
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "join: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) $lines = [];
            $cols[] = $lines;
            $max = max($max, count($lines));
        }
        for ($i=0;$i<$max;$i++) {
            $cells = [];
            foreach ($cols as $c) $cells[] = $c[$i] ?? '';
            echo implode(' ', $cells) . PHP_EOL;
        }
        return 0;
    }
}
