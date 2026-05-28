<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class EnvCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        // parse KEY=VALUE assignments at start
        $assigns = [];
        $i = 0;
        for (; $i < count($argv); $i++) {
            if (strpos($argv[$i], '=') !== false) {
                [$k,$v] = explode('=', $argv[$i], 2);
                $assigns[$k] = $v;
            } else break;
        }
        $cmd = array_slice($argv, $i);
        if (count($cmd) === 0) {
            // print environment with assignments applied
            $env = array_merge($_ENV, $assigns);
            foreach ($env as $k => $v) echo $k . '=' . $v . PHP_EOL;
            return 0;
        }
        // run command with modified environment
        $env = array_merge($_ENV, $assigns);
        // Require pcntl for exec-with-env
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_exec')) {
            fwrite(STDERR, "env: pcntl extension required for this implementation\n");
            return 127;
        }

        $pid = pcntl_fork();
        if ($pid === -1) { fwrite(STDERR, "env: fork failed\n"); return 1; }
        if ($pid === 0) {
            // child: set environment then exec
            foreach ($env as $k => $v) putenv($k . '=' . $v);
            $program = $cmd[0];
            $args = array_slice($cmd, 1);
            pcntl_exec($program, $args, $env);
            exit(127);
        }
        $status = 0;
        pcntl_waitpid($pid, $status);
        if (pcntl_wifexited($status)) return pcntl_wexitstatus($status);
        return 0;
    }
}
