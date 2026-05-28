<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PinkyCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Pure-PHP best-effort: print current user info
        $pw = null;
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pw = posix_getpwuid(posix_geteuid());
        }
        $user = $pw['name'] ?? getenv('USER') ?: get_current_user();
        $tty = is_readable('/dev/tty') ? '/dev/tty' : 'unknown';
        $host = gethostname() ?: 'localhost';
        if ($user) { echo $user . "\t" . $tty . "\t" . $host . PHP_EOL; return 0; }
        fwrite(STDERR, "pinky: no info\n");
        return 1;
    }
}
