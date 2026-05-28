<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class FmtCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $width = 75;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if (($a === '-w' || $a === '--width') && isset($argv[$i+1])) { $width = (int)$argv[++$i]; }
            elseif (preg_match('/^-w(\d+)$/', $a, $m)) { $width = (int)$m[1]; }
            else $files[] = $a;
        }
        $process = function($text) use ($width) {
            $text = preg_replace('/\s+/', ' ', trim($text));
            $words = explode(' ', $text);
            $line = '';
            $out = [];
            foreach ($words as $w) {
                if ($line === '') $line = $w;
                elseif (strlen($line) + 1 + strlen($w) <= $width) $line .= ' ' . $w;
                else { $out[] = $line; $line = $w; }
            }
            if ($line !== '') $out[] = $line;
            return implode(PHP_EOL, $out) . PHP_EOL;
        };
        if (count($files) === 0) {
            $text = stream_get_contents(STDIN);
            // split paragraphs by blank lines
            $paras = preg_split('/\n\s*\n/', trim($text));
            foreach ($paras as $p) echo $process($p) . PHP_EOL;
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "fmt: cannot open '$f'\n"); return 1; }
            $text = file_get_contents($f);
            $paras = preg_split('/\n\s*\n/', trim($text));
            foreach ($paras as $p) echo $process($p) . PHP_EOL;
        }
        return 0;
    }
}
