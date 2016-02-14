<?php
use PhpSandbox\Config;
use PhpSandbox\Evaluator;

/**
 * Class EvaluatorTest
 */
class EvaluatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldEvaluatePhpCode()
    {
        $evaluator = new Evaluator();
        $result = $evaluator->evaluate('<?php echo "blabla";');
        unlink(Config::$tempDir . Evaluator::FILENAME);

        $this->assertEquals('blabla', $result);
    }

    /**
     * @test
     */
    public function shouldReturnLastInsertedCode()
    {
        $code = '<?php echo "blabla";';

        $evaluator = new Evaluator();
        $evaluator->evaluate($code);

        $this->assertEquals($code, $evaluator->getLastCode());
        unlink(Config::$tempDir . Evaluator::FILENAME);
    }

    /**
     * @test
     */
    public function shouldReturnBenchmarks()
    {
        $evaluator = new Evaluator();
        $evaluator->evaluate('<?php echo "blabla";');
        unlink(Config::$tempDir . Evaluator::FILENAME);

        $this->assertTrue(is_numeric($evaluator->getMemoryPeak()));
        $this->assertTrue(is_numeric($evaluator->getMemory()));
        $this->assertTrue(is_numeric($evaluator->getTime()));
    }

    /**
     * @test
     */
    public function shouldReturnErrorMessage()
    {
        $evaluator = new Evaluator();
        $result = $evaluator->evaluate('<?php shell_exec("ls -la");');
        unlink(Config::$tempDir . Evaluator::FILENAME);

        $this->assertRegExp('/has been disabled/', $result);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionFileNotFound()
    {
        $this->setExpectedException('Exception');
        (new Evaluator())->evaluateFile('test.php');
    }
}