<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class UsersCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Pure-PHP best-effort: return the current user
        $user = getenv('USER') ?: get_current_user();
        if ($user) { echo $user . PHP_EOL; return 0; }
        fwrite(STDERR, "users: cannot determine users\n");
        return 1;
    }
}
