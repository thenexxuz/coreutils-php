<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class MvCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) < 2) {
            fwrite(STDERR, "mv: missing file operand\n");
            return 1;
        }
        $src = $argv[0];
        $dest = $argv[1];
        if (!file_exists($src)) {
            fwrite(STDERR, "mv: cannot stat '$src': No such file or directory\n");
            return 1;
        }
        // If dest is directory, move into it
        if (is_dir($dest)) {
            $dest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($src);
        }
        if (@rename($src, $dest)) return 0;
        // fallback to copy+unlink
        $cp = new CpCommand();
        $rc = $cp->run([$src, $dest]);
        if ($rc !== 0) return $rc;
        if (!@unlink($src)) {
            fwrite(STDERR, "mv: failed to remove original '$src'\n");
            return 1;
        }
        return 0;
    }
}
