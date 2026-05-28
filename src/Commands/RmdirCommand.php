<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class RmdirCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $parents = false;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-p') $parents = true;
            else $paths[] = $a;
        }
        if (count($paths) === 0) {
            fwrite(STDERR, "rmdir: missing operand\n");
            return 1;
        }
        foreach ($paths as $p) {
            if (!is_dir($p)) {
                fwrite(STDERR, "rmdir: failed to remove '$p': Not a directory\n");
                return 1;
            }
            if (!@rmdir($p)) {
                fwrite(STDERR, "rmdir: failed to remove '$p'\n");
                return 1;
            }
            if ($parents) {
                $this->removeEmptyParents(dirname($p));
            }
        }
        return 0;
    }

    private function removeEmptyParents(string $path): void
    {
        while ($path !== '' && is_dir($path)) {
            $items = scandir($path);
            if ($items === false) break;
            $count = 0;
            foreach ($items as $it) if ($it !== '.' && $it !== '..') $count++;
            if ($count === 0) {
                @rmdir($path);
                $path = dirname($path);
            } else {
                break;
            }
        }
    }
}
