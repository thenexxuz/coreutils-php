<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CksumCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) {
            $ctx = hash_init('crc32b');
            $len = 0;
            while (!feof(STDIN)) {
                $d = fread(STDIN, 8192);
                if ($d === false) break;
                $len += strlen($d);
                hash_update($ctx, $d);
            }
            $hex = hash_final($ctx);
            $crc = hexdec($hex);
            echo $crc . " " . $len . " -\n";
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) {
                fwrite(STDERR, "cksum: $f: No such file or cannot read\n");
                return 1;
            }
            $len = filesize($f);
            $hex = hash_file('crc32b', $f);
            $crc = hexdec($hex);
            echo $crc . " " . $len . " " . $f . PHP_EOL;
        }
        return 0;
    }
}
