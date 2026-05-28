<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class PrintenvCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) {
            $env = $_ENV;
            // also include getenv() entries not in $_ENV
            if (empty($env)) {
                // try to collect from $_SERVER as fallback
                foreach ($_SERVER as $k => $v) {
                    if (is_string($v)) $env[$k] = $v;
                }
            }
            foreach ($env as $k => $v) echo $k . '=' . $v . PHP_EOL;
            return 0;
        }
        $exit = 0;
        foreach ($argv as $name) {
            $v = getenv($name);
            if ($v === false) { $exit = 1; continue; }
            echo $v . PHP_EOL;
        }
        return $exit;
    }
}
