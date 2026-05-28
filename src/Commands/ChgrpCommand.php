<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ChgrpCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $recursive = false;
        $args = [];
        foreach ($argv as $a) {
            if ($a === '-R') $recursive = true;
            else $args[] = $a;
        }
        if (count($args) < 2) {
            fwrite(STDERR, "chgrp: missing operand\n");
            return 1;
        }
        $group = array_shift($args);
        foreach ($args as $path) {
            if (!file_exists($path)) {
                fwrite(STDERR, "chgrp: cannot access '$path': No such file or directory\n");
                return 1;
            }
            if ($recursive && is_dir($path)) {
                $this->chgrpRecursive($path, $group);
            } else {
                if (!@chgrp($path, $group)) {
                    fwrite(STDERR, "chgrp: failed to change group of '$path'\n");
                    return 1;
                }
            }
        }
        return 0;
    }

    private function chgrpRecursive(string $path, string $group): void
    {
        $items = scandir($path);
        if ($items === false) return;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            if (!@chgrp($p, $group)) {
                // ignore failures in recursion
            }
            if (is_dir($p)) $this->chgrpRecursive($p, $group);
        }
    }
}
