<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class WcCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $showLines = true;
        $showWords = true;
        $showBytes = true;
        $files = [];
        foreach ($argv as $a) {
            if ($a === '-l') { $showWords = false; $showBytes = false; }
            elseif ($a === '-w') { $showLines = false; $showBytes = false; }
            elseif ($a === '-c') { $showLines = false; $showWords = false; }
            else $files[] = $a;
        }
        $processFile = function($f) {
            if ($f === '-') {
                $data = stream_get_contents(STDIN);
                $name = '';
            } else {
                if (!is_readable($f)) { fwrite(STDERR, "wc: $f: cannot open\n"); return [1,0,0,$f]; }
                $data = file_get_contents($f);
                $name = $f;
            }
            $lines = substr_count($data, "\n");
            $words = 0;
            if (strlen($data) > 0) {
                $parts = preg_split('/\s+/', trim($data));
                $words = $parts === false ? 0 : count(array_filter($parts, fn($x)=>$x!==''));
            }
            $bytes = strlen($data);
            return [$lines, $words, $bytes, $name];
        };

        $totLines = $totWords = $totBytes = 0;
        $results = [];
        if (count($files) === 0) $files = ['-'];
        foreach ($files as $f) {
            [$lines,$words,$bytes,$name] = $processFile($f);
            if ($lines === 1 && $words === 0 && $bytes === 0 && $name !== '') return 1;
            $totLines += $lines; $totWords += $words; $totBytes += $bytes;
            $results[] = [$lines,$words,$bytes,$name];
        }
        foreach ($results as [$lines,$words,$bytes,$name]) {
            $out = [];
            if ($showLines) $out[] = sprintf('%7d', $lines);
            if ($showWords) $out[] = sprintf('%7d', $words);
            if ($showBytes) $out[] = sprintf('%7d', $bytes);
            $out[] = $name;
            echo trim(implode(' ', $out)) . PHP_EOL;
        }
        if (count($results) > 1) {
            $out = [];
            if ($showLines) $out[] = sprintf('%7d', $totLines);
            if ($showWords) $out[] = sprintf('%7d', $totWords);
            if ($showBytes) $out[] = sprintf('%7d', $totBytes);
            $out[] = 'total';
            echo trim(implode(' ', $out)) . PHP_EOL;
        }
        return 0;
    }
}
