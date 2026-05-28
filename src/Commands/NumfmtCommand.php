<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class NumfmtCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $to = null; $values = [];
        foreach ($argv as $a) {
            if (strpos($a, '--to=') === 0) $to = substr($a, 5);
            else $values[] = $a;
        }
        if (empty($values)) { fwrite(STDERR, "numfmt: missing operand\n"); return 1; }
        foreach ($values as $v) {
            $n = (float)$v;
            if ($to === 'si') {
                $units = ['','K','M','G','T','P','E']; $i = 0;
                while ($n >= 1000 && $i < count($units)-1) { $n /= 1000; $i++; }
                echo round($n, 1) . $units[$i] . PHP_EOL;
            } elseif ($to === 'iec') {
                $units = ['','K','M','G','T','P']; $i = 0;
                while ($n >= 1024 && $i < count($units)-1) { $n /= 1024; $i++; }
                echo round($n, 1) . $units[$i] . PHP_EOL;
            } else {
                echo (string)$v . PHP_EOL;
            }
        }
        return 0;
    }
}
