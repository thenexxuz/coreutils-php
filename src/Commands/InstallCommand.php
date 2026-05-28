<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class InstallCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $mode = 0644;
        $owner = null;
        $group = null;
        $dest = null;
        $sources = [];
        for ($i=0;$i<count($argv);$i++) {
            $a = $argv[$i];
            if ($a === '-m' && isset($argv[$i+1])) { $mode = octdec($argv[++$i]); }
            elseif ($a === '--owner' && isset($argv[$i+1])) { $owner = $argv[++$i]; }
            elseif ($a === '--group' && isset($argv[$i+1])) { $group = $argv[++$i]; }
            else $sources[] = $a;
        }
        if (count($sources) === 0) { fwrite(STDERR, "install: missing operands\n"); return 1; }
        if (count($sources) === 1) { fwrite(STDERR, "install: missing destination\n"); return 1; }
        $dest = array_pop($sources);
        if (count($sources) > 1 && !is_dir($dest)) { fwrite(STDERR, "install: target '$dest' is not a directory\n"); return 1; }
        foreach ($sources as $src) {
            if (!is_readable($src)) { fwrite(STDERR, "install: cannot stat '$src'\n"); return 1; }
            $base = basename($src);
            $target = is_dir($dest) ? $dest . DIRECTORY_SEPARATOR . $base : $dest;
            if (!copy($src, $target)) { fwrite(STDERR, "install: failed to copy '$src' to '$target'\n"); return 1; }
            @chmod($target, $mode);
            if ($owner !== null) {
                // best-effort: try chown if available
                if (!@chown($target, $owner)) { /* ignore failures */ }
            }
            if ($group !== null) {
                if (!@chgrp($target, $group)) { /* ignore failures */ }
            }
        }
        return 0;
    }
}
