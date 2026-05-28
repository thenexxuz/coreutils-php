<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class BasenameCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            fwrite(STDERR, "basename: missing operand\n");
            return 1;
        }
        $suffix = null;
        if (count($argv) > 1) {
            // last argument is suffix
            $suffix = array_pop($argv);
        }
        foreach ($argv as $p) {
            $b = basename($p);
            if ($suffix !== null && $suffix !== '' && substr($b, -strlen($suffix)) === $suffix) {
                $b = substr($b, 0, strlen($b) - strlen($suffix));
            }
            echo $b . PHP_EOL;
        }
        return 0;
    }
}
