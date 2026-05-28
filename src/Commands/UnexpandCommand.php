<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class UnexpandCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $tabstop = 8;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if (($a === '-t' || $a === '--tabs') && isset($argv[$i+1])) { $tabstop = (int)$argv[++$i]; }
            elseif (preg_match('/^-t(\d+)$/', $a, $m)) { $tabstop = (int)$m[1]; }
            else $files[] = $a;
        }
        $process = function($line) use ($tabstop) {
            // replace sequences of spaces at tab boundaries with tabs
            $out = '';
            $col = 0; $i = 0; $len = strlen($line);
            while ($i < $len) {
                if ($line[$i] === ' ') {
                    // count run of spaces
                    $j = $i;
                    while ($j < $len && $line[$j] === ' ') $j++;
                    $run = $j - $i;
                    // convert as many tabs as possible
                    $tabs = 0;
                    while ($run > 0) {
                        $toBoundary = $tabstop - ($col % $tabstop);
                        if ($run >= $toBoundary) { $tabs++; $col += $toBoundary; $run -= $toBoundary; }
                        else { break; }
                    }
                    if ($tabs > 0) { $out .= str_repeat('\t', $tabs); }
                    $out .= str_repeat(' ', $run);
                    $col += $run; $i = $j;
                } else { $out .= $line[$i]; $col++; $i++; }
            }
            return $out;
        };
        if (count($files) === 0) {
            while (!feof(STDIN)) echo $process(rtrim(fgets(STDIN), "\r\n")) . PHP_EOL;
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "unexpand: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            foreach ($lines as $l) echo $process($l) . PHP_EOL;
        }
        return 0;
    }
}
