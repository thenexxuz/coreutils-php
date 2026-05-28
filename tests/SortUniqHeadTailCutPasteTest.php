<?php
use PHPUnit\Framework\TestCase;

class SortUniqHeadTailCutPasteTest extends TestCase
{
    private $tmpdir;
    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_batchB_' . uniqid();
        mkdir($this->tmpdir);
    }
    protected function tearDown(): void
    {
        if (is_dir($this->tmpdir)) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tmpdir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($it as $file) { @unlink($file->getRealPath()); @rmdir($file->getRealPath()); }
            @rmdir($this->tmpdir);
        }
    }

    public function testSortAndUniq()
    {
        $f = $this->tmpdir . '/in.txt';
        file_put_contents($f, "b\na\na\nc\n");
        exec('php bin/coreutils sort ' . escapeshellarg($f), $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertSame(["a","a","b","c"], $out);

        exec('php bin/coreutils sort -u ' . escapeshellarg($f), $out2, $ret2);
        $this->assertSame(["a","b","c"], $out2);

        exec('php bin/coreutils uniq ' . escapeshellarg($this->tmpdir . '/in_dup.txt') . ' 2>/dev/null || true');
        // create a file with adjacent duplicates
        file_put_contents($this->tmpdir . '/in_dup.txt', "x\nx\ny\n");
        exec('php bin/coreutils uniq ' . escapeshellarg($this->tmpdir . '/in_dup.txt'), $uout, $uret);
        $this->assertSame(["x","y"], $uout);
        exec('php bin/coreutils uniq -c ' . escapeshellarg($this->tmpdir . '/in_dup.txt'), $u2, $r2);
        $this->assertStringContainsString('2 x', implode('\n', $u2));
    }

    public function testHeadTail()
    {
        $f = $this->tmpdir . '/nums.txt';
        $lines = [];
        for ($i=1;$i<=20;$i++) $lines[] = (string)$i;
        file_put_contents($f, implode("\n", $lines) . "\n");
        exec('php bin/coreutils head -n 5 ' . escapeshellarg($f), $h, $rh);
        $this->assertSame(["1","2","3","4","5"], $h);
        exec('php bin/coreutils tail -n 3 ' . escapeshellarg($f), $t, $rt);
        $this->assertSame(["18","19","20"], $t);
    }

    public function testCutAndPaste()
    {
        $f1 = $this->tmpdir . '/a.csv';
        file_put_contents($f1, "a,1\nb,2\n");
        exec('php bin/coreutils cut -d , -f 1 ' . escapeshellarg($f1), $c1, $r1);
        $this->assertSame(["a","b"], $c1);
        $f2 = $this->tmpdir . '/b.csv';
        file_put_contents($f2, "x,9\ny,8\n");
        exec('php bin/coreutils paste -d , ' . escapeshellarg($f1) . ' ' . escapeshellarg($f2), $pout, $rp);
        $this->assertSame(["a,1,x,9","b,2,y,8"], $pout);
    }
}
