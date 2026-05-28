<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class OdCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $files = $argv;
        if (count($files) === 0) $files = ['-'];
        foreach ($files as $f) {
            $data = '';
            if ($f === '-') {
                $data = stream_get_contents(STDIN);
            } else {
                if (!is_readable($f)) { fwrite(STDERR, "od: cannot open '$f'\n"); return 1; }
                $data = file_get_contents($f);
            }
            $bytes = unpack('C*', $data === '' ? "" : $data);
            if ($bytes === false) continue;
            $i = 0;
            foreach ($bytes as $b) {
                if ($i % 16 === 0) printf('%07o ', $i);
                printf('%02x ', $b);
                $i++;
                if ($i % 16 === 0) echo PHP_EOL;
            }
            if ($i % 16 !== 0) echo PHP_EOL;
        }
        return 0;
    }
}
