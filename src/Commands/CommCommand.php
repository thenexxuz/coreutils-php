<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CommCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) < 2) { fwrite(STDERR, "comm: two files required\n"); return 1; }
        [$f1,$f2] = [$argv[0], $argv[1]];
        if (!is_readable($f1) || !is_readable($f2)) { fwrite(STDERR, "comm: cannot read input files\n"); return 1; }
        $a = file($f1, FILE_IGNORE_NEW_LINES);
        $b = file($f2, FILE_IGNORE_NEW_LINES);
        $a = $a === false ? [] : $a;
        $b = $b === false ? [] : $b;
        $ai = $bi = 0; $na = count($a); $nb = count($b);
        while ($ai < $na || $bi < $nb) {
            $av = $ai < $na ? $a[$ai] : null;
            $bv = $bi < $nb ? $b[$bi] : null;
            if ($av !== null && $bv !== null) {
                if ($av === $bv) { echo "\t\t" . $av . PHP_EOL; $ai++; $bi++; }
                elseif ($av < $bv) { echo $av . PHP_EOL; $ai++; }
                else { echo "\t" . $bv . PHP_EOL; $bi++; }
            } elseif ($av !== null) { echo $av . PHP_EOL; $ai++; }
            else { echo "\t" . $b[$bi++] . PHP_EOL; }
        }
        return 0;
    }
}
