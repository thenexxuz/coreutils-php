<?php
use PHPUnit\Framework\TestCase;

class CatTest extends TestCase
{
    private $tmpdir;

    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_test_' . uniqid();
        mkdir($this->tmpdir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpdir . '/*'));
        @rmdir($this->tmpdir);
    }

    public function testCatFileOutputsContents()
    {
        $file = $this->tmpdir . '/f.txt';
        file_put_contents($file, "line1\nline2\n");
        $out = null; $ret = null;
        exec('php bin/coreutils cat ' . escapeshellarg($file), $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertSame(["line1", "line2"], $out);
    }
}
