<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DirCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $ls = new LsCommand();
        return $ls->run($argv);
    }
}
