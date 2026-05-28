<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class FalseCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        return 1;
    }
}
