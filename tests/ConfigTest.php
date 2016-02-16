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
        $config = new Config(__DIR__ . '/../src/config.php');
        $dir = $config->read('tmp_dir');

        $this->assertEquals('/tmp/php_sandbox/', $dir);
        $this->assertTrue($config->has('php_command'));
    }

    /**
     * @test
     */
    public function shouldLoadConfigAndModifyExistingKey()
    {
        $config = (new Config(__DIR__ . '/../src/config.php'))
            ->write('tmp_dir', '/tmp/sandbox_test/');

        $this->assertEquals('/tmp/sandbox_test/', $config->read('tmp_dir'));
    }
}