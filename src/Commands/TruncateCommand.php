<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TruncateCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $size = null;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if (($a === '-s' || $a === '--size') && isset($argv[$i+1])) { $size = $argv[++$i]; }
            elseif (preg_match('/^-s(.+)$/', $a, $m)) { $size = $m[1]; }
            else $files[] = $a;
        }
        if ($size === null) { fwrite(STDERR, "truncate: missing size\n"); return 1; }
        foreach ($files as $f) {
            if (!file_exists($f)) {
                // create empty file
                $h = @fopen($f, 'c');
                if ($h === false) { fwrite(STDERR, "truncate: cannot create '$f'\n"); return 1; }
                fclose($h);
            }
            $num = (int)$size;
            $h = @fopen($f, 'r+b');
            if ($h === false) { fwrite(STDERR, "truncate: cannot open '$f'\n"); return 1; }
            $res = ftruncate($h, $num);
            fclose($h);
            if ($res === false) { fwrite(STDERR, "truncate: failed on '$f'\n"); return 1; }
        }
        return 0;
    }
}
