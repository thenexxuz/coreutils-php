<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class LinkCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) !== 2) {
            fwrite(STDERR, "link: requires target and link name\n");
            return 1;
        }
        [$target,$linkname] = $argv;
        if (!file_exists($target)) { fwrite(STDERR, "link: target '$target' does not exist\n"); return 1; }
        if (file_exists($linkname)) { fwrite(STDERR, "link: '$linkname' already exists\n"); return 1; }
        if (!link($target, $linkname)) { fwrite(STDERR, "link: failed to create link\n"); return 1; }
        return 0;
    }
}
