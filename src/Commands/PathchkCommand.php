<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PathchkCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "pathchk: missing operand\n"); return 1; }
        $rc = 0;
        foreach ($argv as $p) {
            if (strlen($p) > 255) { fwrite(STDERR, "pathchk: '$p' too long\n"); $rc = 1; }
        }
        return $rc;
    }
}
