<?php
use PHPUnit\Framework\TestCase;
use Coreutils\Commands\NiceCommand;
use Coreutils\Commands\NohupCommand;
use Coreutils\Commands\TimeoutCommand;

class PcntlCommandsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_exec')) {
            self::markTestSkipped('pcntl extension required for these tests');
        }
    }

    public function testNiceRunsCommandWithPriority()
    {
        $cmd = new NiceCommand();
        // run /bin/true with niceness 0
        $rc = $cmd->run(['-n', '0', '/bin/true']);
        $this->assertEquals(0, $rc);
    }

    public function testNohupCreatesOutAndReturns()
    {
        $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coreutils_nohup_' . uniqid();
        mkdir($tmpDir);
        $cwd = getcwd();
        try {
            chdir($tmpDir);
            $cmd = new NohupCommand();
            ob_start();
            $rc = $cmd->run(['/bin/true']);
            $outMsg = ob_get_clean();
            $this->assertEquals(0, $rc);
            $this->assertMatchesRegularExpression('/nohup: appending output to \'nohup\.out\' \(pid \d+\)/', trim($outMsg));
            $nohupFile = $tmpDir . DIRECTORY_SEPARATOR . 'nohup.out';
            $this->assertFileExists($nohupFile);
            $this->assertIsWritable($nohupFile);
        } finally {
            chdir($cwd);
            if (isset($nohupFile) && file_exists($nohupFile)) @unlink($nohupFile);
            @rmdir($tmpDir);
        }
    }

    public function testTimeoutExpiresAndReturns124()
    {
        $cmd = new TimeoutCommand();
        // run sleep 2 with timeout 1, expect timeout code 124
        $rc = $cmd->run(['1', '/bin/sleep', '2']);
        $this->assertEquals(124, $rc);
    }
}
