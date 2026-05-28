<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class HeadCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $n = 10;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-n' && isset($argv[$i+1])) { $n = (int)$argv[++$i]; }
            elseif (preg_match('/^-n(\d+)$/', $a, $m)) { $n = (int)$m[1]; }
            else $files[] = $a;
        }
        if (count($files) === 0) {
            $count = 0;
            while (!feof(STDIN) && $count < $n) { echo rtrim(fgets(STDIN), "\r\n") . PHP_EOL; $count++; }
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "head: cannot open '$f' for reading\n"); return 1; }
            $fh = fopen($f, 'rb');
            $count = 0;
            while (!feof($fh) && $count < $n) { echo rtrim(fgets($fh), "\r\n") . PHP_EOL; $count++; }
            fclose($fh);
        }
        return 0;
    }
}
