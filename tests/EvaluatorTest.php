<?php
use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;

/**
 * Class EvaluatorTest
 */
class EvaluatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $sampleCode = '<?php $i = 0; while ($i < 10) { echo ++$i; }';

    /**
     * @return Evaluator
     */
    private function getEvaluator()
    {
        return new Evaluator($this->getConfig());
    }

    private function getConfig()
    {
        return (new Config(__DIR__ . '/../src/config.php'))
        ->write('tmp_dir', '/tmp/sandbox_test/')
        ->write('disable_functions', ['shell_exec']);
    }

    /**
     * remove code.php file after test
     */
    private function clear()
    {
        unlink($this->getConfig()->read('tmp_dir') . Evaluator::FILENAME);
        rmdir($this->getConfig()->read('tmp_dir'));
    }

    /**
     * @test
     */
    public function shouldEvaluatePhpCode()
    {
        $evaluator = $this->getEvaluator();
        $result = $evaluator->evaluate($this->sampleCode);
        $this->clear();

        $this->assertEquals('12345678910', $result);
    }

    /**
     * @test
     */
    public function shouldReturnLastInsertedCode()
    {
        $evaluator = $this->getEvaluator();
        $evaluator->evaluate($this->sampleCode);

        $this->assertEquals($this->sampleCode, $evaluator->getLastCode());
        $this->clear();
    }

    /**
     * @test
     */
    public function shouldReturnBenchmarks()
    {
        $evaluator = $this->getEvaluator();
        $evaluator->evaluate($this->sampleCode);
        $this->clear();

        $this->assertTrue(is_numeric($evaluator->getMemoryPeak()));
        $this->assertTrue(is_numeric($evaluator->getMemory()));
        $this->assertTrue(is_numeric($evaluator->getTime()));
    }

    /**
     * @test
     */
    public function shouldReturnErrorMessage()
    {
        $evaluator = $this->getEvaluator();
        $result = $evaluator->evaluate('<?php shell_exec("ls -la");');
        $this->clear();

        $this->assertRegExp('/has been disabled/', $result);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionFileNotFound()
    {
        $this->setExpectedException('Exception');
        $this->getEvaluator()->evaluateFile('test.php');
    }
}