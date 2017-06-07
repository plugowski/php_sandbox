<?php
use PhpSandbox\Evaluator\Config;

/**
 * Class ConfigTest
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadConfigAndGetCorrectValue()
    {
        $config = new Config(__DIR__ . '/../config/config.php');
        $command = $config->read('fast_cgi_hosts');

        $this->assertEquals('php56', $command['5.6']);
        $this->assertTrue($config->has('tmp_dir'));
    }

    /**
     * @test
     */
    public function shouldLoadConfigAndModifyExistingKey()
    {
        $config = (new Config(__DIR__ . '/../config/config.php'))
            ->write('tmp_dir', '/tmp/sandbox_test/');

        $this->assertEquals('/tmp/sandbox_test/', $config->read('tmp_dir'));
    }
}