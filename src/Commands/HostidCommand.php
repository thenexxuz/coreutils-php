<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class HostidCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (function_exists('hostid')) {
            echo hostid() . PHP_EOL;
            return 0;
        }
        // fallback: use md5 of hostname truncated to 8 hex digits like many hostid implementations
        $hn = gethostname() ?: 'unknown';
        $hex = substr(md5($hn), 0, 8);
        echo $hex . PHP_EOL;
        return 0;
    }
}
