<?php
use PHPUnit\Framework\TestCase;

class TextEnvUtilsTest extends TestCase
{
    private function runCmd(string $cmd, array &$out = null, &$exit = null)
    {
        exec($cmd, $out, $exit);
        return implode("\n", $out);
    }

    public function testWhoamiAndPrintenv()
    {
        $out = null; $exit = null;
        $res = $this->runCmd('php bin/coreutils whoami', $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertNotEmpty(trim($res));

        $out = null; $exit = null;
        $res = $this->runCmd('php bin/coreutils printenv PATH', $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertNotEmpty(trim($res));
    }

    public function testBasenameDirname()
    {
        $out = null; $exit = null;
        $res = $this->runCmd('php bin/coreutils basename /foo/bar/baz.txt', $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertStringContainsString('baz.txt', $res);

        $out = null; $exit = null;
        $res = $this->runCmd('php bin/coreutils dirname /foo/bar/baz.txt', $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertStringContainsString('/foo/bar', $res);
    }

    public function testTrAndWc()
    {
        $out = null; $exit = null;
        $cmd = "printf 'a\\nb\\nc\\n' | php bin/coreutils tr 'a-c' 'x-z'";
        $res = $this->runCmd($cmd, $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertStringContainsString('x', $res);

        $out = null; $exit = null;
        $cmd = "printf 'one two\nthree\n' | php bin/coreutils wc -w";
        $res = $this->runCmd($cmd, $out, $exit);
        $this->assertEquals(0, $exit);
        $this->assertStringContainsString('3', $res);
    }
}
