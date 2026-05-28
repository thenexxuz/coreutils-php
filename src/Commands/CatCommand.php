<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CatCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            // read from STDIN
            while (!feof(STDIN)) {
                echo fgets(STDIN);
            }
            return 0;
        }

        foreach ($argv as $file) {
            if ($file === '-') {
                while (!feof(STDIN)) {
                    echo fgets(STDIN);
                }
                continue;
            }
            if (!is_readable($file)) {
                fwrite(STDERR, "cat: $file: No such file or cannot read\n");
                return 1;
            }
            $fh = fopen($file, 'rb');
            if ($fh === false) {
                fwrite(STDERR, "cat: $file: failed to open\n");
                return 1;
            }
            while (!feof($fh)) {
                echo fread($fh, 8192);
            }
            fclose($fh);
        }
        return 0;
    }
}
