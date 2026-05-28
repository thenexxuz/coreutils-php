<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class VdirCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // vdir is a variant of ls -l
        $ls = new LsCommand();
        // add -l to argv if not present
        $hasL = false;
        foreach ($argv as $a) if ($a === '-l') $hasL = true;
        if (!$hasL) array_unshift($argv, '-l');
        return $ls->run($argv);
    }
}
