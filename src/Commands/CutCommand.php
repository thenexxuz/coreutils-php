<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class CutCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $delim = "\t";
        $fields = null;
        $files = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-d' && isset($argv[$i+1])) { $delim = $argv[++$i]; }
            elseif (preg_match('/^-d(.+)$/', $a, $m)) { $delim = $m[1]; }
            elseif ($a === '-f' && isset($argv[$i+1])) { $fields = $argv[++$i]; }
            elseif (preg_match('/^-f(.+)$/', $a, $m)) { $fields = $m[1]; }
            else $files[] = $a;
        }
        if ($fields === null) { fwrite(STDERR, "cut: you must specify a field list with -f\n"); return 1; }
        $fieldIndexes = $this->parseFields($fields);
        if (count($files) === 0) {
            while (!feof(STDIN)) { $line = rtrim(fgets(STDIN), "\r\n"); echo $this->cutLine($line, $delim, $fieldIndexes) . PHP_EOL; }
            return 0;
        }
        foreach ($files as $f) {
            if (!is_readable($f)) { fwrite(STDERR, "cut: cannot open '$f'\n"); return 1; }
            $lines = file($f, FILE_IGNORE_NEW_LINES);
            if ($lines === false) continue;
            foreach ($lines as $line) echo $this->cutLine($line, $delim, $fieldIndexes) . PHP_EOL;
        }
        return 0;
    }

    private function parseFields(string $spec): array
    {
        $res = [];
        $parts = explode(',', $spec);
        foreach ($parts as $p) {
            if (strpos($p, '-') !== false) {
                [$a,$b] = array_map('trim', explode('-', $p, 2));
                $a = ($a === '') ? 1 : (int)$a;
                $b = ($b === '') ? PHP_INT_MAX : (int)$b;
                for ($i=$a;$i<=$b;$i++) $res[$i] = true;
            } else {
                $i = (int)trim($p);
                if ($i>0) $res[$i] = true;
            }
        }
        ksort($res);
        return array_keys($res);
    }

    private function cutLine(string $line, string $delim, array $fieldIndexes): string
    {
        $parts = explode($delim, $line);
        $out = [];
        foreach ($fieldIndexes as $idx) {
            $i = $idx - 1;
            $out[] = $parts[$i] ?? '';
        }
        return implode($delim, $out);
    }
}
