<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TsortCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $lines = [];
        if (count($argv) > 0) {
            foreach ($argv as $f) {
                if (!is_readable($f)) { fwrite(STDERR, "tsort: cannot open '$f'\n"); return 1; }
                $lines = array_merge($lines, file($f, FILE_IGNORE_NEW_LINES));
            }
        } else {
            while (!feof(STDIN)) $lines[] = rtrim(fgets(STDIN), "\r\n");
        }
        $graph = [];
        $indeg = [];
        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) < 2) continue;
            $a = $parts[0]; $b = $parts[1];
            $graph[$a][] = $b;
            $indeg[$b] = ($indeg[$b] ?? 0) + 1;
            if (!isset($indeg[$a])) $indeg[$a] = $indeg[$a] ?? 0;
        }
        $queue = [];
        foreach ($indeg as $node => $d) if ($d === 0) $queue[] = $node;
        $out = [];
        while (!empty($queue)) {
            $n = array_shift($queue);
            $out[] = $n;
            foreach ($graph[$n] ?? [] as $m) {
                $indeg[$m]--;
                if ($indeg[$m] === 0) $queue[] = $m;
            }
        }
        foreach ($out as $n) echo $n . PHP_EOL;
        return 0;
    }
}
