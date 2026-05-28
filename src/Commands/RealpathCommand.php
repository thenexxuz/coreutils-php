<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class RealpathCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            echo getcwd() . PHP_EOL;
            return 0;
        }
        $exit = 0;
        foreach ($argv as $p) {
            $rp = realpath($p);
            if ($rp === false) { fwrite(STDERR, "realpath: $p: No such file or directory\n"); $exit = 1; continue; }
            echo $rp . PHP_EOL;
        }
        return $exit;
    }
}
