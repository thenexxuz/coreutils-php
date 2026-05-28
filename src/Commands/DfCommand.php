<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DfCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $paths = $argv;
        if (count($paths) === 0) $paths = [getcwd()];
        // header
        echo sprintf("%-20s %10s %10s %10s %4s %% %s", 'Filesystem', '1K-blocks', 'Used', 'Available', 'Use%', 'Mounted on') . PHP_EOL;
        foreach ($paths as $p) {
            $total = @disk_total_space($p);
            $free = @disk_free_space($p);
            if ($total === false || $free === false) {
                fwrite(STDERR, "df: cannot read filesystem info for $p\n");
                continue;
            }
            $used = $total - $free;
            $kbTotal = (int)floor($total / 1024);
            $kbUsed = (int)floor($used / 1024);
            $kbFree = (int)floor($free / 1024);
            $pct = $kbTotal > 0 ? (int)round($kbUsed / $kbTotal * 100) : 0;
            $fs = $p;
            echo sprintf('%-20s %10d %10d %10d %4d%% %s', $fs, $kbTotal, $kbUsed, $kbFree, $pct, $p) . PHP_EOL;
        }
        return 0;
    }
}

