<?php
use PHPUnit\Framework\TestCase;

class PwdTrueFalseTest extends TestCase
{
    public function testPwdReturnsCwd()
    {
        $cwd = getcwd();
        $out = null; $ret = null;
        exec('php bin/coreutils pwd', $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertSame([$cwd], $out);
    }

    public function testTrueReturnsZero()
    {
        exec('php bin/coreutils true', $out, $ret);
        $this->assertSame(0, $ret);
    }

    public function testFalseReturnsNonZero()
    {
        exec('php bin/coreutils false', $out, $ret);
        $this->assertNotSame(0, $ret);
    }
}
