<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TimeoutCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) < 2) { fwrite(STDERR, "timeout: usage: timeout DURATION COMMAND...\n"); return 2; }
        $duration = (int)array_shift($argv);
        $cmd = $argv;
        // Require pcntl for robust process control
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_exec')) {
            fwrite(STDERR, "timeout: pcntl extension required for this implementation\n");
            return 127;
        }

        // Prefer pcntl for robust process control
        if (function_exists('pcntl_fork') && function_exists('pcntl_exec')) {
            $pid = pcntl_fork();
            if ($pid === -1) { fwrite(STDERR, "timeout: fork failed\n"); return 1; }
            if ($pid === 0) {
                // child: execute program
                $program = $cmd[0];
                $args = array_slice($cmd, 1);
                pcntl_exec($program, $args);
                // if exec fails
                exit(127);
            }
            // parent: wait with timeout
            $start = time();
            $status = 0;
            while (true) {
                $w = pcntl_waitpid($pid, $status, WNOHANG);
                if ($w == $pid) {
                    if (pcntl_wifexited($status)) return pcntl_wexitstatus($status);
                    return 0;
                }
                if (time() - $start >= $duration) {
                    // try graceful terminate
                    if (function_exists('posix_kill')) posix_kill($pid, SIGTERM);
                    // wait a bit
                    usleep(200000);
                    // force kill
                    if (function_exists('posix_kill')) posix_kill($pid, SIGKILL);
                    pcntl_waitpid($pid, $status);
                    if (pcntl_wifexited($status)) return pcntl_wexitstatus($status);
                    return 124; // timeout exit code
                }
                usleep(100000);
            }
        }
        // Should not reach here because pcntl is required above.
        return 1;
    }
}
