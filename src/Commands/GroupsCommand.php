<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class GroupsCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (function_exists('posix_getgroups')) {
            $gids = posix_getgroups();
            $names = [];
            foreach ($gids as $g) {
                if (function_exists('posix_getgrgid')) {
                    $gi = posix_getgrgid($g);
                    $names[] = $gi['name'] ?? (string)$g;
                } else {
                    $names[] = (string)$g;
                }
            }
            echo implode(' ', $names) . PHP_EOL;
            return 0;
        }
        // fallback: try primary group
        if (function_exists('posix_getegid') && function_exists('posix_getgrgid')) {
            $gid = posix_getegid();
            $gr = posix_getgrgid($gid);
            if ($gr && isset($gr['name'])) {
                echo $gr['name'] . PHP_EOL;
                return 0;
            }
        }
        fwrite(STDERR, "groups: cannot determine groups\n");
        return 1;
    }
}
