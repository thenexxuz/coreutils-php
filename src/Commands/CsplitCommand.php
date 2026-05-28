<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CsplitCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) < 2) { fwrite(STDERR, "csplit: usage: csplit file pattern...\n"); return 1; }
        $file = array_shift($argv);
        if (!is_readable($file)) { fwrite(STDERR, "csplit: cannot open '$file'\n"); return 1; }
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $outIndex = 0; $outLines = [];
        foreach ($argv as $pat) {
            // support simple regex like /regex/
            if (preg_match('#^/(.*)/$#', $pat, $m)) {
                $regex = $m[1];
                for ($i = 0; $i < count($lines); $i++) {
                    if (preg_match('#' . $regex . '#', $lines[$i])) {
                        // write current buffer to file
                        $name = sprintf('xx%02d', $outIndex++);
                        file_put_contents($name, implode("\n", $outLines) . (count($outLines)?"\n":""));
                        $outLines = [];
                        continue;
                    }
                    $outLines[] = $lines[$i];
                }
            }
        }
        // final piece
        $name = sprintf('xx%02d', $outIndex++);
        file_put_contents($name, implode("\n", $outLines) . (count($outLines)?"\n":""));
        return 0;
    }
}
