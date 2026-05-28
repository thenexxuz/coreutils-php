<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class EchoCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $noNewline = false;
        if (isset($argv[0]) && $argv[0] === '-n') {
            $noNewline = true;
            array_shift($argv);
        }
        $out = implode(' ', $argv);
        if ($noNewline) {
            echo $out;
        } else {
            echo $out . PHP_EOL;
        }
        return 0;
    }
}
