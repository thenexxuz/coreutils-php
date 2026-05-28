<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ArchCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // arch is typically uname -m
        $arch = php_uname('m');
        echo ($arch ?: PHP_OS) . PHP_EOL;
        return 0;
    }
}
