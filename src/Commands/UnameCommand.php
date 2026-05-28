<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class UnameCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // support flags similar to uname: -a, -s, -n, -r, -v, -m, -p, -i, -o
        $opts = [];
        foreach ($argv as $a) {
            if (substr($a,0,1) === '-') {
                $flags = str_split(ltrim($a,'-'));
                foreach ($flags as $f) $opts[$f] = true;
            }
        }
        $sys = php_uname();
        $parts = [];
        if (empty($opts) || isset($opts['a'])) {
            // full
            echo php_uname('s') . ' ' . php_uname('n') . ' ' . php_uname('r') . ' ' . php_uname('v') . ' ' . php_uname('m') . PHP_EOL;
            return 0;
        }
        if (isset($opts['s'])) $parts[] = php_uname('s');
        if (isset($opts['n'])) $parts[] = php_uname('n');
        if (isset($opts['r'])) $parts[] = php_uname('r');
        if (isset($opts['v'])) $parts[] = php_uname('v');
        if (isset($opts['m'])) $parts[] = php_uname('m');
        if (isset($opts['p'])) $parts[] = php_uname('p') ?: php_uname('m');
        if (isset($opts['i'])) $parts[] = php_uname('i') ?: php_uname('m');
        if (isset($opts['o'])) $parts[] = php_uname('o') ?: PHP_OS;
        echo implode(' ', $parts) . PHP_EOL;
        return 0;
    }
}
