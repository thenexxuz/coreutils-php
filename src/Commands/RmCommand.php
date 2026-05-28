<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class RmCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $recursive = false;
        $targets = [];
        foreach ($argv as $a) {
            if ($a === '-r' || $a === '-R') {
                $recursive = true;
            } else {
                $targets[] = $a;
            }
        }
        if (count($targets) === 0) {
            fwrite(STDERR, "rm: missing operand\n");
            return 1;
        }
        foreach ($targets as $t) {
            if (!file_exists($t)) {
                fwrite(STDERR, "rm: cannot remove '$t': No such file or directory\n");
                return 1;
            }
            if (is_dir($t)) {
                if (!$recursive) {
                    fwrite(STDERR, "rm: cannot remove '$t': Is a directory\n");
                    return 1;
                }
                if (!$this->removeRecursive($t)) return 1;
            } else {
                if (!@unlink($t)) {
                    fwrite(STDERR, "rm: failed to remove '$t'\n");
                    return 1;
                }
            }
        }
        return 0;
    }

    private function removeRecursive(string $path): bool
    {
        if (!is_dir($path)) return @unlink($path);
        $items = scandir($path);
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            if (is_dir($p)) {
                if (!$this->removeRecursive($p)) return false;
            } else {
                if (!@unlink($p)) return false;
            }
        }
        return @rmdir($path);
    }
}
