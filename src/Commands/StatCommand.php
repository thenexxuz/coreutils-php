<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class StatCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            fwrite(STDERR, "stat: missing operand\n");
            return 1;
        }
        foreach ($argv as $f) {
            if (!file_exists($f)) { fwrite(STDERR, "stat: cannot stat '$f': No such file or directory\n"); return 1; }
            $s = stat($f);
            if ($s === false) { fwrite(STDERR, "stat: failed to stat '$f'\n"); return 1; }
            echo "$f:\n";
            echo "  Size: " . $s['size'] . "\n";
            echo "  Blocks: " . (int)$s['blocks'] . "\n";
            echo "  IO Block: " . (int)$s['blksize'] . "\n";
            echo "  Device: " . (int)$s['dev'] . "\n";
            echo "  Inode: " . (int)$s['ino'] . "\n";
            echo "  Links: " . (int)$s['nlink'] . "\n";
            echo "  Access: " . date('Y-m-d H:i:s', $s['atime']) . "\n";
            echo "  Modify: " . date('Y-m-d H:i:s', $s['mtime']) . "\n";
            echo "  Change: " . date('Y-m-d H:i:s', $s['ctime']) . "\n";
        }
        return 0;
    }
}

