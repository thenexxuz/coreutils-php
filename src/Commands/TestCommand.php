<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class TestCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $n = count($argv);
        if ($n === 0) return 1;
        // unary operators
        if ($n === 1) {
            // test STRING -> true if non-empty
            return ($argv[0] !== '') ? 0 : 1;
        }
        if ($n === 2) {
            $op = $argv[0]; $val = $argv[1];
            switch ($op) {
                case '-e': return file_exists($val) ? 0 : 1;
                case '-f': return is_file($val) ? 0 : 1;
                case '-d': return is_dir($val) ? 0 : 1;
                case '-z': return (strlen($val) === 0) ? 0 : 1;
                default: fwrite(STDERR, "test: unknown operator $op\n"); return 2;
            }
        }
        else {
            $left = $argv[0]; $op = $argv[1]; $right = $argv[2];
            switch ($op) {
                case '=': return ($left === $right) ? 0 : 1;
                case '!=': return ($left !== $right) ? 0 : 1;
                case '-eq': return ((int)$left === (int)$right) ? 0 : 1;
                case '-ne': return ((int)$left !== (int)$right) ? 0 : 1;
                case '-gt': return ((int)$left > (int)$right) ? 0 : 1;
                case '-lt': return ((int)$left < (int)$right) ? 0 : 1;
                case '-ge': return ((int)$left >= (int)$right) ? 0 : 1;
                case '-le': return ((int)$left <= (int)$right) ? 0 : 1;
                default: fwrite(STDERR, "test: unknown binary operator $op\n"); return 2;
            }
        }
        return 2;
    }
}
