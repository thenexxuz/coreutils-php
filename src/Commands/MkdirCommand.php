<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class MkdirCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $parents = false;
        $mode = 0777;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-p') $parents = true;
            elseif (preg_match('/^-m(.+)$/', $a, $m)) {
                $mode = intval($m[1], 8);
            } else {
                $paths[] = $a;
            }
        }
        if (count($paths) === 0) {
            fwrite(STDERR, "mkdir: missing operand\n");
            return 1;
        }
        foreach ($paths as $p) {
            if (file_exists($p)) {
                if ($parents && is_dir($p)) {
                    continue;
                }
                fwrite(STDERR, "mkdir: cannot create directory '$p': File exists\n");
                return 1;
            }
            if ($parents) {
                if (!@mkdir($p, $mode, true)) {
                    fwrite(STDERR, "mkdir: failed to create directory '$p'\n");
                    return 1;
                }
            } else {
                if (!@mkdir($p, $mode)) {
                    fwrite(STDERR, "mkdir: failed to create directory '$p'\n");
                    return 1;
                }
            }
        }
        return 0;
    }
}
