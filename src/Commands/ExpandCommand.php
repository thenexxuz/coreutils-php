<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ExpandCommand implements CommandInterface
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
            $out = '';
            $col = 0;
            for ($i=0;$i<strlen($line);$i++) {
                $c = $line[$i];
                if ($c === "\t") {
                    $spaces = $tabstop - ($col % $tabstop);
                    $out .= str_repeat(' ', $spaces);
                    $col += $spaces;
                } else { $out .= $c; $col++; }
            }
            return $out;
        };
        if (count($files) === 0) {
            while (!feof(STDIN)) echo $process(rtrim(fgets(STDIN), "\r\n")) . PHP_EOL;
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "expand: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            foreach ($lines as $l) echo $process($l) . PHP_EOL;
        }
        return 0;
    }
}
