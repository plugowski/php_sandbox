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
    public function shouldGetCorrectConfigValue()
    {
        $config = new Config(__DIR__ . '/../src/config.php');
        $dir = $config->read('tmp_dir');

        $this->assertEquals('/tmp/', $dir);
        $this->assertTrue($config->has('php_command'));
    }
}