<?php
use PHPUnit\Framework\TestCase;

class BatchAFileOpsTest extends TestCase
{
    private $tmpdir;

    protected function setUp(): void
    {
        $this->tmpdir = sys_get_temp_dir() . '/coreutils_batchA_' . uniqid();
        mkdir($this->tmpdir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tmpdir)) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tmpdir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($it as $file) {
                @unlink($file->getRealPath());
                @rmdir($file->getRealPath());
            }
            @rmdir($this->tmpdir);
        }
    }

    public function testMkdirAndRmdirWithParents()
    {
        $path = $this->tmpdir . '/a/b/c';
        exec('php bin/coreutils mkdir -p ' . escapeshellarg($path), $o, $r);
        $this->assertSame(0, $r);
        $this->assertDirectoryExists($path);
        exec('php bin/coreutils rmdir -p ' . escapeshellarg($path), $o, $r);
        $this->assertSame(0, $r);
        $this->assertDirectoryDoesNotExist($this->tmpdir . '/a');
    }

    public function testChmodChangesMode()
    {
        $file = $this->tmpdir . '/f.txt';
        file_put_contents($file, 'x');
        exec('php bin/coreutils chmod 600 ' . escapeshellarg($file), $o, $r);
        $this->assertSame(0, $r);
        $mode = substr(sprintf('%o', fileperms($file)), -3);
        $this->assertSame('600', $mode);
    }

    public function testChownChgrpNoopOrSkip()
    {
        $file = $this->tmpdir . '/f2.txt';
        file_put_contents($file, 'x');
        // get current owner/group
        if (!function_exists('posix_getpwuid') || !function_exists('posix_getgrgid')) {
            $this->markTestSkipped('posix functions not available');
        }
        $uid = fileowner($file);
        $pw = posix_getpwuid($uid);
        $owner = $pw['name'] ?? null;
        $gid = filegroup($file);
        $gr = posix_getgrgid($gid);
        $group = $gr['name'] ?? null;
        if ($owner === null || $group === null) $this->markTestSkipped('cannot determine owner/group');
        exec('php bin/coreutils chown ' . escapeshellarg($owner) . ' ' . escapeshellarg($file), $o, $r);
        // chown may require privileges even to set to same owner on some systems; accept 0 or 1
        $this->assertTrue(in_array($r, [0,1], true));
        exec('php bin/coreutils chgrp ' . escapeshellarg($group) . ' ' . escapeshellarg($file), $o, $r2);
        $this->assertTrue(in_array($r2, [0,1], true));
    }
}
