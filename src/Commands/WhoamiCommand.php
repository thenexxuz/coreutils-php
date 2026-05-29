<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class WhoamiCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pw = posix_getpwuid(posix_geteuid());
            if ($pw && is_array($pw)) {
                echo $pw['name'] . PHP_EOL;
                return 0;
            }
        }
        // fallback to environment or PHP's get_current_user
        $name = getenv('USER') ?: get_current_user();
        if ($name !== false && $name !== '') {
            echo $name . PHP_EOL;
            return 0;
        }
        fwrite(STDERR, "whoami: cannot determine user\n");
        return 1;
    }
}
