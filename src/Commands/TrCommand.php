<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TrCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $delete = false;
        $sets = [];
        foreach ($argv as $a) {
            if ($a === '-d') { $delete = true; continue; }
            $sets[] = $a;
        }
        if (count($sets) < 1) {
            fwrite(STDERR, "tr: missing operand\n");
            return 1;
        }
        $set1 = $this->expandSet($sets[0]);
        $set2 = isset($sets[1]) ? $this->expandSet($sets[1]) : '';

        $in = stream_get_contents(STDIN);
        $out = '';
        for ($i = 0, $n = strlen($in); $i < $n; $i++) {
            $c = $in[$i];
            $pos = array_search($c, $set1, true);
            if ($pos === false) {
                $out .= $c;
            } else {
                if ($delete) {
                    // drop it
                } else {
                    if ($pos < count($set2)) $out .= $set2[$pos];
                    else $out .= end($set2) ?: $c;
                }
            }
        }
        echo $out;
        return 0;
    }

    private function expandSet(string $s): array
    {
        $chars = [];
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $c = $s[$i];
            if ($c === '\\' && $i+1 < $len) {
                $i++;
                $esc = $s[$i];
                if ($esc === 'n') $chars[] = "\n";
                elseif ($esc === 't') $chars[] = "\t";
                elseif ($esc === 'r') $chars[] = "\r";
                else $chars[] = $esc;
            } elseif ($i+2 < $len && $s[$i+1] === '-') {
                // range a-z
                $start = ord($c);
                $end = ord($s[$i+2]);
                if ($start <= $end) {
                    for ($k = $start; $k <= $end; $k++) $chars[] = chr($k);
                } else {
                    for ($k = $start; $k >= $end; $k--) $chars[] = chr($k);
                }
                $i += 2;
            } else {
                $chars[] = $c;
            }
        }
        return $chars;
    }
}
