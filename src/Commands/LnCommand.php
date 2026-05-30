<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class LnCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $symbolic = false;
        $force = false;
        $args = [];
        foreach ($argv as $a) {
            if ($a === '-s') {
                $symbolic = true;
            } elseif ($a === '-f') {
                $force = true;
            } elseif (strlen($a) > 2 && $a[0] === '-' && $a[1] !== '-') {
                $ok = true;
                for ($i = 1; $i < strlen($a); $i++) {
                    if ($a[$i] === 's') {
                        $symbolic = true;
                    } elseif ($a[$i] === 'f') {
                        $force = true;
                    } else {
                        $ok = false;
                        break;
                    }
                }
                if (!$ok) {
                    $args[] = $a;
                }
            } else {
                $args[] = $a;
            }
        }
        if (count($args) < 2) {
            fwrite(STDERR, "ln: missing operand\n");
            return 1;
        }
        $target = $args[0];
        $link = $args[1];

        if ($force && (file_exists($link) || is_link($link))) {
            if (is_dir($link) && !is_link($link)) {
                fwrite(STDERR, "ln: failed to create link '$link' -> '$target'\n");
                return 1;
            }
            if (!@unlink($link)) {
                fwrite(STDERR, "ln: failed to create link '$link' -> '$target'\n");
                return 1;
            }
        }

        if ($symbolic) {
            if (!@symlink($target, $link)) {
                fwrite(STDERR, "ln: failed to create symbolic link '$link' -> '$target'\n");
                return 1;
            }
            return 0;
        }
        if (!file_exists($target)) {
            fwrite(STDERR, "ln: failed to access '$target'\n");
            return 1;
        }
        if (!@link($target, $link)) {
            fwrite(STDERR, "ln: failed to create link '$link' -> '$target'\n");
            return 1;
        }
        return 0;
    }
}
