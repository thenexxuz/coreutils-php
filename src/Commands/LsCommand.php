<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class LsCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $showAll = false;
        $long = false;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-a') {
                $showAll = true;
            } elseif ($a === '-l') {
                $long = true;
            } else {
                $paths[] = $a;
            }
        }
        if (count($paths) === 0) {
            $paths[] = getcwd();
        }

        $first = true;
        foreach ($paths as $p) {
            if (count($paths) > 1) {
                if (!$first) echo PHP_EOL;
                echo $p . ":\n";
                $first = false;
            }
            if (is_dir($p)) {
                $items = scandir($p);
                if ($items === false) {
                    fwrite(STDERR, "ls: cannot access '$p'\n");
                    continue;
                }
                foreach ($items as $it) {
                    if (!$showAll && ($it === '.' || $it === '..')) continue;
                    $full = $p . DIRECTORY_SEPARATOR . $it;
                    if ($long) {
                        $perms = $this->formatPerms($full);
                        $nlink = file_exists($full) ? (string)filetype($full) : '-';
                        $size = is_file($full) ? filesize($full) : 0;
                        $owner = function_exists('posix_getpwuid') ? (@posix_getpwuid(@fileowner($full))['name'] ?? fileowner($full)) : fileowner($full);
                        $group = function_exists('posix_getgrgid') ? (@posix_getgrgid(@filegroup($full))['name'] ?? filegroup($full)) : filegroup($full);
                        $mtime = date('Y-m-d H:i', filemtime($full));
                        echo sprintf('%s %2s %s %s %8d %s %s', $perms, $nlink, $owner, $group, $size, $mtime, $it) . PHP_EOL;
                    } else {
                        echo $it . PHP_EOL;
                    }
                }
            } else {
                echo basename($p) . PHP_EOL;
            }
        }
        return 0;
    }

    private function formatPerms(string $path): string
    {
        if (!file_exists($path)) return '----------';
        $perms = fileperms($path);
        $info = '';
        $info .= ($perms & 0x4000) ? 'd' : '-';
        $info .= ($perms & 0x0100) ? 'r' : '-';
        $info .= ($perms & 0x0080) ? 'w' : '-';
        $info .= ($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-');
        $info .= ($perms & 0x0020) ? 'r' : '-';
        $info .= ($perms & 0x0010) ? 'w' : '-';
        $info .= ($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-');
        $info .= ($perms & 0x0004) ? 'r' : '-';
        $info .= ($perms & 0x0002) ? 'w' : '-';
        $info .= ($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-');
        return $info;
    }
}
