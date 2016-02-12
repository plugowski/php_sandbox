<?php
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

        $this->assertEquals('blabla', $result);
    }

    /**
     * @test
     */
    public function shouldReturnErrorMessage()
    {
        $evaluator = new Evaluator();
        $result = $evaluator->evaluate('<?php shell_exec("ls -la");');

        $this->assertRegExp('/has been disabled/', $result);
    }
}