<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DuCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $summarize = false;
        $paths = [];
        foreach ($argv as $a) {
            if ($a === '-s') $summarize = true;
            else $paths[] = $a;
        }
        if (count($paths) === 0) $paths = ['.'];
        foreach ($paths as $p) {
            if (!file_exists($p)) { fwrite(STDERR, "du: $p: No such file or directory\n"); continue; }
            if ($summarize) {
                $size = $this->sizeRecursive($p);
                echo $size . "\t" . $p . PHP_EOL;
            } else {
                $this->printRecursive($p);
            }
        }
        return 0;
    }

    private function sizeRecursive(string $path): int
    {
        if (is_file($path)) return filesize($path);
        $total = 0;
        $items = scandir($path);
        if ($items === false) return 0;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            $total += $this->sizeRecursive($p);
        }
        return $total;
    }

    private function printRecursive(string $path): void
    {
        if (is_file($path)) { echo filesize($path) . "\t" . $path . PHP_EOL; return; }
        $items = scandir($path);
        if ($items === false) return;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            $this->printRecursive($p);
        }
        echo $this->sizeRecursive($path) . "\t" . $path . PHP_EOL;
    }
}
