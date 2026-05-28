<?php
use PHPUnit\Framework\TestCase;

class FilesystemBatchATwoTest extends TestCase
{
    private $tmpdir;
    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_fs2_' . uniqid();
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

    public function testDuSummaryAndDfStatFind()
    {
        mkdir($this->tmpdir . '/a');
        file_put_contents($this->tmpdir . '/a/f1', 'hello');
        file_put_contents($this->tmpdir . '/a/f2', 'world');
        // du -s
        exec('php bin/coreutils du -s ' . escapeshellarg($this->tmpdir . '/a'), $dout, $dr);
        $this->assertSame(0, $dr);
        $this->assertStringContainsString($this->tmpdir . '/a', $dout[0]);
        // stat
        exec('php bin/coreutils stat ' . escapeshellarg($this->tmpdir . '/a/f1'), $sout, $sr);
        $this->assertSame(0, $sr);
        $this->assertStringContainsString('Size:', implode('\n', $sout));
        // find -name
        exec('php bin/coreutils find ' . escapeshellarg($this->tmpdir) . ' -name f1', $fout, $fr);
        $this->assertSame(0, $fr);
        $this->assertSame([$this->tmpdir . '/a/f1'], $fout);
        // df (just ensure runs)
        exec('php bin/coreutils df ' . escapeshellarg($this->tmpdir), $dfout, $dfr);
        $this->assertSame(0, $dfr);
        $this->assertStringContainsString('Filesystem', $dfout[0]);
    }
}
