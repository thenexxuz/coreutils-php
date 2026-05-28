<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class NohupCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "nohup: missing command\n"); return 1; }
        $cmd = $argv[0];
        $args = array_slice($argv, 1);
        $out = 'nohup.out';
        $descriptors = [
            0 => ['file', '/dev/null', 'r'],
            1 => ['file', $out, 'a'],
            2 => ['file', $out, 'a'],
        ];
        // spawn process without invoking external nohup binary
        // Require pcntl for proper detaching
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_exec')) {
            fwrite(STDERR, "nohup: pcntl extension required for this implementation\n");
            return 127;
        }

        // fork and detach
        $pid = pcntl_fork();
        if ($pid === -1) { fwrite(STDERR, "nohup: fork failed\n"); return 1; }
        if ($pid === 0) {
            // child: detach
            if (function_exists('posix_setsid')) @posix_setsid();
            // redirect stdio
            $stdin = fopen('/dev/null', 'r');
            $stdout = fopen($out, 'a');
            $stderr = fopen($out, 'a');
            if ($stdin) {
                fclose(STDIN);
                define('STDIN', fopen('/dev/null', 'r'));
            }
            if ($stdout) {
                fclose(STDOUT);
                define('STDOUT', fopen($out, 'a'));
            }
            if ($stderr) {
                fclose(STDERR);
                define('STDERR', fopen($out, 'a'));
            }
            @chdir('/');
            pcntl_exec($cmd, $args);
            exit(127);
        }

        // parent: record message including pid
        @touch($out);
        echo "nohup: appending output to '" . $out . "' (pid " . (int)$pid . ")\n";
        return 0;
    }
}
