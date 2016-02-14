<?php
use PhpSandbox\Evaluator\Config;
use PhpSandbox\Evaluator\Evaluator;

/**
 * Class EvaluatorTest
 */
class EvaluatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return Evaluator
     */
    private function getEvaluator()
    {
        return new Evaluator(new Config(__DIR__ . '/../src/config.php'));
    }

    /**
     * remove code.php file after test
     */
    private function clear()
    {
        unlink((new Config(__DIR__ . '/../src/config.php'))->read('tmp_dir') . Evaluator::FILENAME);
    }

    /**
     * @test
     */
    public function shouldEvaluatePhpCode()
    {
        $evaluator = $this->getEvaluator();
        $result = $evaluator->evaluate('<?php echo "blabla";');
        $this->clear();

        $this->assertEquals('blabla', $result);
    }

    /**
     * @test
     */
    public function shouldReturnLastInsertedCode()
    {
        $code = '<?php echo "blabla";';

        $evaluator = $this->getEvaluator();
        $evaluator->evaluate($code);

        $this->assertEquals($code, $evaluator->getLastCode());
        $this->clear();
    }

    /**
     * @test
     */
    public function shouldReturnBenchmarks()
    {
        $evaluator = $this->getEvaluator();
        $evaluator->evaluate('<?php echo "blabla";');
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