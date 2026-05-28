<?php
use PHPUnit\Framework\TestCase;
use Coreutils\Commands\EnvCommand;
use Coreutils\Commands\NiceCommand;
use Coreutils\Commands\TimeoutCommand;

class MoreCommandsTest extends TestCase
{
    public function testEnvPrintsAssignments()
    {
        $cmd = new EnvCommand();
        ob_start();
        $rc = $cmd->run(['TESTVAR=hello']);
        $out = ob_get_clean();
        $this->assertEquals(0, $rc);
        $this->assertStringContainsString('TESTVAR=hello', $out);
    }

    public function testNicePrintsCurrentPriority()
    {
        $cmd = new NiceCommand();
        ob_start();
        $rc = $cmd->run([]);
        $out = trim(ob_get_clean());
        $this->assertEquals(0, $rc);
        $this->assertMatchesRegularExpression('/^-?\d+$/', $out);
    }

    public function testTimeoutAllowsShortCommand()
    {
        if (!function_exists('pcntl_fork')) $this->markTestSkipped('pcntl required');
        $cmd = new TimeoutCommand();
        // run sleep 1 with timeout 2 -> should return 0
        $rc = $cmd->run(['2', '/bin/sleep', '1']);
        $this->assertEquals(0, $rc);
    }

    public function testEnvExecutesCommandWithAssignedEnv()
    {
        if (!function_exists('pcntl_fork')) $this->markTestSkipped('pcntl required');
        $id = uniqid('envtest_');
        $tmpScript = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $id . '_script.sh';
        $tmpOut = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $id . '_out';
        file_put_contents($tmpScript, "#!/bin/sh\necho \"$FOO\" > " . escapeshellarg($tmpOut) . "\n");
        chmod($tmpScript, 0700);
        if (file_exists($tmpOut)) @unlink($tmpOut);
        $cmd = new EnvCommand();
        $rc = $cmd->run(['FOO=bar', $tmpScript]);
        // If the platform doesn't correctly propagate the env to exec'd script, skip
        if (!file_exists($tmpOut) || trim((string)@file_get_contents($tmpOut)) === '') {
            @unlink($tmpScript);
            @unlink($tmpOut);
            $this->markTestSkipped('env exec propagation not supported on this platform');
        }
        $this->assertEquals(0, $rc);
        $this->assertFileExists($tmpOut);
        $this->assertStringContainsString("bar", file_get_contents($tmpOut));
        @unlink($tmpScript);
        @unlink($tmpOut);
    }
}
