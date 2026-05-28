<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class FoldCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $width = 80;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if (($a === '-w' || $a === '--width') && isset($argv[$i+1])) { $width = (int)$argv[++$i]; }
            elseif (preg_match('/^-w(\d+)$/', $a, $m)) { $width = (int)$m[1]; }
            else $files[] = $a;
        }
        $wrap = function($line, $width) {
            $out = '';
            while (strlen($line) > $width) {
                $out .= substr($line, 0, $width) . PHP_EOL;
                $line = substr($line, $width);
            }
            $out .= $line . PHP_EOL;
            return $out;
        };
        if (count($files) === 0) {
            while (!feof(STDIN)) { $line = rtrim(fgets(STDIN), "\r\n"); echo $wrap($line, $width); }
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "fold: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            foreach ($lines as $line) echo $wrap($line, $width);
        }
        return 0;
    }
}
