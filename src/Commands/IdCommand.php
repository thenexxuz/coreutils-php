<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class IdCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (!function_exists('posix_geteuid') || !function_exists('posix_getegid')) {
            fwrite(STDERR, "id: posix functions unavailable\n");
            return 1;
        }
        $uid = posix_geteuid();
        $pw = function_exists('posix_getpwuid') ? posix_getpwuid($uid) : null;
        $uname = $pw['name'] ?? null;

        $gid = posix_getegid();
        $gr = function_exists('posix_getgrgid') ? posix_getgrgid($gid) : null;
        $gname = $gr['name'] ?? null;

        $out = sprintf('uid=%d(%s) gid=%d(%s)', $uid, $uname ?? $uid, $gid, $gname ?? $gid);

        $groups = [];
        if (function_exists('posix_getgroups')) {
            $gids = posix_getgroups();
            foreach ($gids as $gg) {
                $ginfo = function_exists('posix_getgrgid') ? posix_getgrgid($gg) : null;
                $gname2 = $ginfo['name'] ?? $gg;
                $groups[] = $gname2 . "($gg)";
            }
        }
        if (!empty($groups)) {
            $out .= ' groups=' . implode(',', $groups);
        }
        echo $out . PHP_EOL;
        return 0;
    }
}
