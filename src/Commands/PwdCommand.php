<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PwdCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        echo getcwd() . PHP_EOL;
        return 0;
    }
}
