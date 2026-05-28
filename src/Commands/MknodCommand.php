<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class MknodCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "mknod: missing operand\n"); return 1; }
        $rc = 0;
        foreach ($argv as $path) {
            if (file_exists($path)) { fwrite(STDERR, "mknod: '$path' already exists\n"); $rc = 1; continue; }
            // best-effort: create a regular file
            if (@touch($path) === false) { fwrite(STDERR, "mknod: cannot create '$path'\n"); $rc = 1; }
        }
        return $rc;
    }
}
