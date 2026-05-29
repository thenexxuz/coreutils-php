<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PrintfCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "printf: missing format\n"); return 1; }
        $fmt = array_shift($argv);
        // interpret C-style escapes in format
        $fmt = str_replace('\\n', "\n", $fmt);
        $out = '';
        if (count($argv) === 0) {
            $out = sprintf($fmt, '');
        } else {
            // apply format repeatedly if fewer args than conversions
            $vals = $argv;
            // use vsprintf
            try {
                $res = @vsprintf($fmt, $vals);
                $out = ($res === false) ? '' : $res;
            } catch (\Throwable $e) {
                $out = '';
            }
        }
        echo $out;
        return 0;
    }
}
