<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class DdCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $if = null; $of = null; $bs = 512; $count = null;
        foreach ($argv as $a) {
            if (strpos($a, 'if=') === 0) $if = substr($a, 3);
            elseif (strpos($a, 'of=') === 0) $of = substr($a, 3);
            elseif (strpos($a, 'bs=') === 0) $bs = (int)substr($a, 3);
            elseif (strpos($a, 'count=') === 0) $count = (int)substr($a, 6);
        }
        $in = $if ? @fopen($if, 'rb') : STDIN;
        $out = $of ? @fopen($of, 'wb') : STDOUT;
        if ($in === false) { fwrite(STDERR, "dd: cannot open input\n"); return 1; }
        if ($out === false) { fwrite(STDERR, "dd: cannot open output\n"); return 1; }
        $blocks = 0;
        while (!feof($in)) {
            if ($count !== null && $blocks >= $count) break;
            $data = fread($in, $bs);
            if ($data === false || $data === '') break;
            fwrite($out, $data);
            $blocks++;
        }
        if ($if) fclose($in);
        if ($of) fclose($out);
        return 0;
    }
}
