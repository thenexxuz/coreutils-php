<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SyncCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Best-effort: flush PHP output buffers and return success; actual filesystem sync
        // is a privileged OS operation not exposed in pure PHP.
        while (ob_get_level() > 0) ob_end_flush();
        return 0;
    }
}
