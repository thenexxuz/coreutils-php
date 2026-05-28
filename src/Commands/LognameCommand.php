<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class LognameCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $name = getenv('LOGNAME') ?: getenv('USER');
        if ($name) { echo $name . PHP_EOL; return 0; }
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pw = posix_getpwuid(posix_geteuid());
            if ($pw && isset($pw['name'])) { echo $pw['name'] . PHP_EOL; return 0; }
        }
        fwrite(STDERR, "logname: cannot determine login name\n");
        return 1;
    }
}
