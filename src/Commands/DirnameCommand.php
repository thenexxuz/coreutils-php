<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DirnameCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            echo '.' . PHP_EOL;
            return 0;
        }
        foreach ($argv as $p) {
            echo dirname($p) . PHP_EOL;
        }
        return 0;
    }
}
