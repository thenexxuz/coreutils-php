<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ExprCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        if (count($argv) === 0) { fwrite(STDERR, "expr: missing operand\n"); return 2; }
        // join arguments with spaces
        $expr = implode(' ', $argv);
        // allow only digits, spaces and operators
        if (!preg_match('/^[0-9\s+\-\*\/%%()<>!=|&]+$/', $expr)) { fwrite(STDERR, "expr: bad expression\n"); return 2; }
        // map '|' and '&' logical operators to PHP equivalents
        $php = str_replace('%', ' % ', $expr);
        try {
            // evaluate safely
            $result = 0;
            // simple operations: try using eval with validation
            $eval = '@(' . $expr . ');';
            $r = null;
            set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline): bool {
                return true;
            });
            $r = eval('return ' . $expr . ';');
            restore_error_handler();
            if ($r === null) { fwrite(STDERR, "expr: evaluation error\n"); return 2; }
            echo $r . PHP_EOL;
            return 0;
        } catch (\Throwable $e) {
            fwrite(STDERR, "expr: evaluation error\n");
            return 2;
        }
    }
}
