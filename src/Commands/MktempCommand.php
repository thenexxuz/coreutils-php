<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class MktempCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $dir = sys_get_temp_dir();
        $prefix = 'tmp.';
        $makeDir = false;
        foreach ($argv as $a) {
            if ($a === '-d') $makeDir = true;
            elseif (strpos($a, '--tmpdir=') === 0) $dir = substr($a, 9);
            else $prefix = $a;
        }
        $tmp = tempnam($dir, $prefix);
        if ($tmp === false) { fwrite(STDERR, "mktemp: failed\n"); return 1; }
        if ($makeDir) {
            // create directory with unique name
            $d = $tmp . '.d';
            if (!mkdir($d)) { fwrite(STDERR, "mktemp: failed to create dir\n"); return 1; }
            echo $d . PHP_EOL;
            return 0;
        }
        echo $tmp . PHP_EOL;
        return 0;
    }
}
