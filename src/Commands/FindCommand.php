<?php
namespace Coreutils\Commands;

use Coreutils\CommandInterface;

class FindCommand implements CommandInterface
{
    public function run(array $argv): int
    {
        $start = '.';
        $name = null;
        foreach ($argv as $a) {
            if ($a === '-name') {
                // next arg is pattern
            } elseif ($start === '.') {
                $start = $a;
            }
        }
        // parse -name
        for ($i=0;$i<count($argv);$i++) {
            if ($argv[$i] === '-name' && isset($argv[$i+1])) { $name = $argv[$i+1]; }
        }
        $this->scan($start, $name);
        return 0;
    }

    private function scan(string $path, ?string $name): void
    {
        if (!is_dir($path)) { if ($this->matches($path, $name)) echo $path . PHP_EOL; return; }
        $items = scandir($path);
        if ($items === false) return;
        foreach ($items as $it) {
            if ($it === '.' || $it === '..') continue;
            $p = $path . DIRECTORY_SEPARATOR . $it;
            if ($this->matches($p, $name)) echo $p . PHP_EOL;
            if (is_dir($p)) $this->scan($p, $name);
        }
    }

    private function matches(string $path, ?string $name): bool
    {
        if ($name === null) return true;
        return fnmatch($name, basename($path));
    }
}
