<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class NlCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) {
            $content = stream_get_contents(STDIN);
            $lines = explode("\n", $content);
            $num = 1;
            foreach ($lines as $i => $line) {
                // keep trailing newline behavior
                if ($i === count($lines)-1 && $line === '') break;
                printf("%6d\t%s\n", $num++, $line);
            }
            return 0;
        }
        foreach ($files as $f) {
            if (!file_exists($f)) { fwrite(STDERR, "nl: $f: No such file or directory\n"); return 1; }
            $num = 1;
            $fh = fopen($f, 'r');
            while (($line = fgets($fh)) !== false) {
                printf("%6d\t%s", $num++, $line);
            }
            fclose($fh);
        }
        return 0;
    }
}
