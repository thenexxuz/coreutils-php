<?php
use PHPUnit\Framework\TestCase;

class FilesystemCommandsTest extends TestCase
{
    private $tmpdir;

    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_fs_' . uniqid();
        mkdir($this->tmpdir);
    }

    protected function tearDown(): void
    {
        // best-effort cleanup
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tmpdir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }
        @rmdir($this->tmpdir);
    }

    public function testLsShowsFiles()
    {
        file_put_contents($this->tmpdir . '/a.txt', "a");
        file_put_contents($this->tmpdir . '/b.txt', "b");
        $cmd = 'cd ' . escapeshellarg($this->tmpdir) . ' && php ' . escapeshellarg(getcwd() . '/bin/coreutils') . ' ls';
        exec($cmd, $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertContains('a.txt', $out);
        $this->assertContains('b.txt', $out);
    }

    public function testCpAndMvAndRmAndLn()
    {
        $src = $this->tmpdir . '/src.txt';
        file_put_contents($src, "hello");
        $dest = $this->tmpdir . '/dest.txt';
        // cp
        exec('php bin/coreutils cp ' . escapeshellarg($src) . ' ' . escapeshellarg($dest), $o, $r);
        $this->assertSame(0, $r);
        $this->assertFileExists($dest);
        $this->assertSame("hello", file_get_contents($dest));
        // mv
        $moved = $this->tmpdir . '/moved.txt';
        exec('php bin/coreutils mv ' . escapeshellarg($dest) . ' ' . escapeshellarg($moved), $o, $r);
        $this->assertSame(0, $r);
        $this->assertFileExists($moved);
        $this->assertFileDoesNotExist($dest);
        // ln -s
        $link = $this->tmpdir . '/link.txt';
        exec('php bin/coreutils ln -s ' . escapeshellarg($moved) . ' ' . escapeshellarg($link), $o, $r);
        $this->assertSame(0, $r);
        $this->assertTrue(is_link($link));
        // rm
        exec('php bin/coreutils rm ' . escapeshellarg($link), $o, $r);
        $this->assertSame(0, $r);
        $this->assertFileDoesNotExist($link);
    }
}
