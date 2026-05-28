<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class SumCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) $files = ['-'];
        foreach ($files as $f) {
            if ($f === '-') { $data = stream_get_contents(STDIN); $name = ''; }
            else {
                if (!is_readable($f)) { fwrite(STDERR, "sum: cannot open '$f'\n"); return 1; }
                $data = file_get_contents($f); $name = $f;
            }
            $bytes = strlen($data);
            $blocks = (int)ceil($bytes / 1024);
            echo $blocks . "\t" . $name . PHP_EOL;
        }
        return 0;
    }
}
