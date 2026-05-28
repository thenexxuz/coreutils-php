<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ChmodCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $recursive = false;
        $mode = null;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-R') $recursive = true;
            elseif ($mode === null) $mode = $a;
            else $paths[] = $a;
        }
        if ($mode === null || count($paths) === 0) {
            fwrite(STDERR, "chmod: missing operand\n");
            return 1;
        }
        // parse numeric mode (e.g., 644 or 0644)
        if (preg_match('/^[0-7]+$/', $mode)) {
            $m = intval($mode, 8);
        } else {
            fwrite(STDERR, "chmod: only numeric modes supported in this implementation\n");
            return 1;
        }
        foreach ($paths as $p) {
            if (!file_exists($p)) {
                fwrite(STDERR, "chmod: cannot access '$p': No such file or directory\n");
                return 1;
            }
            if (is_dir($p) && $recursive) {
                $this->chmodRecursive($p, $m);
            } else {
                if (!@chmod($p, $m)) {
                    fwrite(STDERR, "chmod: failed to change mode of '$p'\n");
                    return 1;
                }
            }
        }
        return 0;
    }

    private function chmodRecursive(string $path, int $mode): void
    {
        $items = scandir($path);
        if ($items === false) return;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            if (is_dir($p)) {
                @chmod($p, $mode);
                $this->chmodRecursive($p, $mode);
            } else {
                @chmod($p, $mode);
            }
        }
    }
}
