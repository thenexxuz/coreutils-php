<?php
use PHPUnit\Framework\TestCase;

class HashCommandsTest extends TestCase
{
    private $tmpdir;
    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_hash_' . uniqid();
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

    public function testMd5Sha256Cksum()
    {
        $file = $this->tmpdir . '/h.txt';
        file_put_contents($file, "hello world\n");
        // md5
        exec('php bin/coreutils md5sum ' . escapeshellarg($file), $out, $r);
        $this->assertSame(0, $r);
        $expectedMd5 = hash_file('md5', $file) . '  ' . $file;
        $this->assertSame([$expectedMd5], $out);
        // sha256
        exec('php bin/coreutils sha256sum ' . escapeshellarg($file), $sout, $sr);
        $this->assertSame(0, $sr);
        $this->assertSame([hash_file('sha256', $file) . '  ' . $file], $sout);
        // cksum
        exec('php bin/coreutils cksum ' . escapeshellarg($file), $cout, $cr);
        $this->assertSame(0, $cr);
        $hex = hash_file('crc32b', $file);
        $crc = hexdec($hex);
        $len = filesize($file);
        $this->assertSame([$crc . ' ' . $len . ' ' . $file], $cout);
    }
}
