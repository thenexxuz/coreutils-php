<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SplitCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $linesPerFile = 1000;
        $prefix = 'x';
        $file = null;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-l' && isset($argv[$i+1])) { $linesPerFile = (int)$argv[++$i]; }
            elseif ($a === '-a' && isset($argv[$i+1])) { /* ignore suffix length */ $i++; }
            elseif ($a === '-d') { /* ignore */ }
            else $files[] = $a;
        }
        $input = ['-'];
        if (count($files) > 0) $input = $files;
        foreach ($input as $infile) {
            $lines = [];
            if ($infile === '-') {
                while (!feof(STDIN)) $lines[] = rtrim(fgets(STDIN), "\r\n");
            } else {
                if (!is_readable($infile)) { fwrite(STDERR, "split: cannot open '$infile'\n"); return 1; }
                $lines = file($infile, FILE_IGNORE_NEW_LINES);
            }
            $count = 0; $part = 0;
            while ($count < count($lines)) {
                $chunk = array_slice($lines, $count, $linesPerFile);
                $name = $prefix . str_pad($part, 2, 'a', STR_PAD_LEFT);
                file_put_contents($name, implode(PHP_EOL, $chunk) . PHP_EOL);
                $part++; $count += count($chunk);
            }
        }
        return 0;
    }
}
