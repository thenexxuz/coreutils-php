<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class ChownCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $recursive = false;
        $args = [];
        foreach ($argv as $a) {
            if ($a === '-R') $recursive = true;
            else $args[] = $a;
        }
        if (count($args) < 2) {
            fwrite(STDERR, "chown: missing operand\n");
            return 1;
        }
        $owner = array_shift($args);
        foreach ($args as $path) {
            if (!file_exists($path)) {
                fwrite(STDERR, "chown: cannot access '$path': No such file or directory\n");
                return 1;
            }
            if ($recursive && is_dir($path)) {
                $this->chownRecursive($path, $owner);
            } else {
                if (!@chown($path, $owner)) {
                    fwrite(STDERR, "chown: failed to change owner of '$path'\n");
                    return 1;
                }
            }
        }
        return 0;
    }

    private function chownRecursive(string $path, string $owner): void
    {
        $items = scandir($path);
        if ($items === false) return;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            if (!@chown($p, $owner)) {
                // ignore failures in recursion
            }
            if (is_dir($p)) $this->chownRecursive($p, $owner);
        }
    }
}
