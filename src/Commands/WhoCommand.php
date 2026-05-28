<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class WhoCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Pure-PHP best-effort: report current user and terminal when available
        $user = getenv('USER') ?: (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? '' : '');
        if ($user) {
            $tty = is_readable('/dev/tty') ? 'tty' : 'unknown';
            $time = date('Y-m-d H:i');
            echo sprintf("%s\t%s\t%s\n", $user, $tty, $time);
            return 0;
        }
        return 0;
    }
}
