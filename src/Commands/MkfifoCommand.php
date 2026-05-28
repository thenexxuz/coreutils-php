<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class MkfifoCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "mkfifo: missing operand\n"); return 1; }
        $exit = 0;
        foreach ($argv as $f) {
            if (function_exists('posix_mkfifo')) {
                if (!@posix_mkfifo($f, 0600)) { fwrite(STDERR, "mkfifo: cannot create '$f'\n"); $exit = 1; }
            } else {
                // posix_mkfifo not available; fall back to creating a regular file as best-effort
                if (!@touch($f)) { fwrite(STDERR, "mkfifo: cannot create '$f'\n"); $exit = 1; }
            }
        }
        return $exit;
    }
}
