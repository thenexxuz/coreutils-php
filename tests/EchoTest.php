<?php
use PHPUnit\Framework\TestCase;

class EchoTest extends TestCase
{
    public function testEchoOutputsArgsWithNewline()
    {
        $out = null; $ret = null;
        exec('php bin/coreutils echo hello world', $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertSame(["hello world"], $out);
    }

    public function testEchoNoNewline()
    {
        $out = null; $ret = null;
        exec('php bin/coreutils echo -n nosuffix', $out, $ret);
        $this->assertSame(0, $ret);
        $this->assertSame(["nosuffix"], $out);
    }
}
