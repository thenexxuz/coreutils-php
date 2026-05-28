<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TrueCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        return 0;
    }
}
