<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class Md5sumCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) {
            // read stdin
            $ctx = hash_init('md5');
            while (!feof(STDIN)) {
                $data = fread(STDIN, 8192);
                if ($data === false) break;
                hash_update($ctx, $data);
            }
            $sum = hash_final($ctx);
            echo $sum . "  -\n";
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) {
                fwrite(STDERR, "md5sum: $f: No such file or cannot read\n");
                return 1;
            }
            $sum = hash_file('md5', $f);
            echo $sum . "  " . $f . PHP_EOL;
        }
        return 0;
    }
}
