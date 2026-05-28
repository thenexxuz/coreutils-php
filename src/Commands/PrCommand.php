<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PrCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // Minimal implementation: print files with page headers suppressed
        $files = $argv;
        if (count($files) === 0) {
            while (!feof(STDIN)) echo fgets(STDIN);
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "pr: $f: cannot open\n"); return 1; }
            $content = file_get_contents($f);
            echo $content;
        }
        return 0;
    }
}
