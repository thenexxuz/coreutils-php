<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CpCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $recursive = false;
        $args = [];
        foreach ($argv as $a) {
            if ($a === '-r' || $a === '-R') {
                $recursive = true;
            } else {
                $args[] = $a;
            }
        }
        if (count($args) < 2) {
            fwrite(STDERR, "cp: missing operand\n");
            return 1;
        }
        $dest = array_pop($args);
        foreach ($args as $src) {
            if (!file_exists($src)) {
                fwrite(STDERR, "cp: cannot stat '$src': No such file or directory\n");
                return 1;
            }
            $target = $dest;
            if (is_dir($dest)) {
                $target = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($src);
            }
            if (is_dir($src)) {
                if (!$recursive) {
                    fwrite(STDERR, "cp: -r not specified; omitting directory '$src'\n");
                    return 1;
                }
                $ok = $this->copyRecursive($src, $target);
                if (!$ok) return 1;
            } else {
                if (!@copy($src, $target)) {
                    fwrite(STDERR, "cp: failed to copy '$src' to '$target'\n");
                    return 1;
                }
            }
        }
        return 0;
    }

    private function copyRecursive(string $src, string $dst): bool
    {
        if (!is_dir($src)) return @copy($src, $dst);
        if (!is_dir($dst) && !mkdir($dst, 0755, true)) return false;
        $items = scandir($src);
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $s = $src . DIRECTORY_SEPARATOR . $it;
            $d = $dst . DIRECTORY_SEPARATOR . $it;
            if (is_dir($s)) {
                if (!$this->copyRecursive($s, $d)) return false;
            } else {
                if (!@copy($s, $d)) return false;
            }
        }
        return true;
    }
}
