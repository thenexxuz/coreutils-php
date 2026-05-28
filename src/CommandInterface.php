<?php
namespace Coreutils;

interface CommandInterface
{
    /**
     * Run the command. Receives argv (arguments only, program name removed).
     * Should return an integer exit code.
     *
     * @param array $argv
     * @return int
     */
    public function run(array $argv): int;
}
