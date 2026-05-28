<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class YesCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $str = count($argv) ? implode(' ', $argv) : 'y';
        // infinite output until killed
        while (true) {
            echo $str . PHP_EOL;
            if (connection_aborted()) break;
        }
        return 0;
    }
}
