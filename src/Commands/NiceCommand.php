<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class NiceCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $n = 10; // default niceness when using -n
        $args = $argv;
        if (isset($args[0]) && $args[0] === '-n' && isset($args[1])) { $n = (int)$args[1]; array_shift($args); array_shift($args); }
        if (count($args) === 0) {
            // print current priority
            if (function_exists('posix_getpriority')) {
                $cur = @posix_getpriority(PRIO_PROCESS, 0);
                echo (string)$cur . PHP_EOL;
                return 0;
            }
            echo "0\n";
            return 0;
        }

        $cmd = array_shift($args);
        // Require pcntl for true priority handling
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_exec')) {
            fwrite(STDERR, "nice: pcntl extension required for this implementation\n");
            return 127;
        }

        // Child process will set priority and exec
        $pid = pcntl_fork();
        if ($pid === -1) { fwrite(STDERR, "nice: fork failed\n"); return 1; }
        if ($pid === 0) {
            // child: set priority if possible
            if (function_exists('posix_setpriority')) {
                // GNU nice niceness range: -20 (highest) .. 19 (lowest)
                $n = max(-20, min(19, $n));
                @posix_setpriority(PRIO_PROCESS, 0, $n);
            }
            // execute command
            pcntl_exec($cmd, $args);
            // exec failed
            exit(127);
        }
        $status = 0;
        pcntl_waitpid($pid, $status);
        if (function_exists('pcntl_wifexited') && pcntl_wifexited($status)) return pcntl_wexitstatus($status);
        return 0;
    }
}
