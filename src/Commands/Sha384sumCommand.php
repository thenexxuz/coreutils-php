<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class Sha384sumCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        return $this->runHash('sha384', $argv);
    }

    private function runHash(string $algo, array $files): int
    {
        if (count($files) === 0) {
            $ctx = hash_init($algo);
            while (!feof(STDIN)) {
                $d = fread(STDIN, 8192);
                if ($d === false) break;
                hash_update($ctx, $d);
            }
            echo hash_final($ctx) . "  -\n";
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) {
                fwrite(STDERR, "sha384sum: $f: No such file or cannot read\n");
                return 1;
            }
            echo hash_file($algo, $f) . "  " . $f . PHP_EOL;
        }
        return 0;
    }
}
