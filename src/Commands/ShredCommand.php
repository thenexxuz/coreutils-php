<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ShredCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "shred: missing operand\n"); return 1; }
        $passes = 3; $rc = 0;
        foreach ($argv as $f) {
            if (!is_writable($f)) { fwrite(STDERR, "shred: cannot open '$f'\n"); $rc = 1; continue; }
            $size = filesize($f);
            if ($size === false) $size = 0;
            $fh = fopen($f, 'c+');
            if (!$fh) { fwrite(STDERR, "shred: cannot open '$f'\n"); $rc = 1; continue; }
            for ($p = 0; $p < $passes; $p++) {
                fseek($fh, 0);
                if ($size > 0) fwrite($fh, random_bytes($size));
                fflush($fh);
            }
            fclose($fh);
        }
        return $rc;
    }
}
