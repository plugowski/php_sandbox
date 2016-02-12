<?php
namespace PhpSandbox;

/**
 * Class Evaluator
 * @package PhpSandbox
 */
class Evaluator
{
    /**
     * @param string $code
     * @return string
     */
    public function evaluate($code)
    {
        $filename = Config::$tempDir . 'code.php';

        $fp = fopen($filename, 'w');
        fwrite($fp, $code);
        fclose($fp);

        chmod($filename, 0755);

        return $this->evaluateFile($filename);
    }

    /**
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    public function evaluateFile($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('File not found: ' . $filename);
        }

        $cmd = [
            Config::$phpCommand,
            '-d disable_functions=' . implode(',', Config::$disable_functions),
            $this->getDirectivesString(),
            $filename,
            '3>&1 1>&1 2>&1'
        ];

        return shell_exec(implode(' ', $cmd));
    }

    /**
     * @return string
     */
    private function getDirectivesString()
    {
        $cmd = '';
        foreach (Config::$other_directives as $name => $value) {
            $cmd = '-d ' . $name . '=' . $value;
        }
        return $cmd;
    }

}