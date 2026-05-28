<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TacCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) {
            $lines = [];
            while (!feof(STDIN)) $lines[] = rtrim(fgets(STDIN), "\r\n");
            $lines = array_reverse($lines);
            foreach ($lines as $l) echo $l . PHP_EOL;
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "tac: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            $lines = array_reverse($lines);
            foreach ($lines as $l) echo $l . PHP_EOL;
        }
        return 0;
    }
}
