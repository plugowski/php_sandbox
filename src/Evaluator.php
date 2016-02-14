<?php
namespace PhpSandbox;

/**
 * Class Evaluator
 * @package PhpSandbox
 */
class Evaluator
{
    const FILENAME = 'code.php';
    const BENCHMARK_PATTERN = '/#benchmark#/';

    /**
     * @var array
     */
    private $benchmarks = [
        'memory' => 'memory_get_usage()',
        'memory_peak' => 'memory_get_peak_usage()'
    ];
    /**
     * @var int
     */
    private $memory;
    /**
     * @var int
     */
    private $memoryPeak;
    /**
     * @var int
     */
    private $time;

    /**
     * @param string $code
     * @return string
     */
    public function evaluate($code)
    {
        $filename = Config::$tempDir . self::FILENAME;
        $code = $this->insertBenchmarks($code);

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

        $start = microtime(true);

        $output = shell_exec(implode(' ', $cmd));

        $this->time = microtime(true) - $start;

        return $this->parseOutput($output);

    }

    /**
     * @param $code
     * @return string
     */
    private function insertBenchmarks($code)
    {
        if (!empty($this->benchmarks)) {
            $list = [];
            $insert = 'echo PHP_EOL . \'#benchmark#%s;';
            foreach ($this->benchmarks as $name => $function) {
                $list[] = '#' . $name . ':\' . ' . $function;
            }
            $code .= PHP_EOL . sprintf($insert, implode(' . \'', $list));
        }
        return $code;
    }

    /**
     * @param string $input
     * @return string
     */
    private function parseOutput($input)
    {
        $output = explode(PHP_EOL, $input);

        if (preg_match(self::BENCHMARK_PATTERN, end($output))) {
            $benchmark = str_replace('#benchmark#', '', array_pop($output));
            if (preg_match_all('/#([^:]+):([^#]+)/', $benchmark, $matches)) {
                $data = array_combine($matches[1], $matches[2]);
                $this->memory = isset($data['memory']) ? (int)$data['memory'] : 0;
                $this->memoryPeak = isset($data['memory_peak']) ? (int)$data['memory_peak'] : 0;
            }
        }

        return implode(PHP_EOL, $output);
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

    /**
     * @return string
     */
    public function getLastCode()
    {
        $file = file_get_contents(Config::$tempDir . self::FILENAME);
        $contents = explode(PHP_EOL, $file);

        if (preg_match(self::BENCHMARK_PATTERN, end($contents))) {
            array_pop($contents);
        }
        return implode(PHP_EOL, $contents);
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @return int
     */
    public function getMemoryPeak()
    {
        return $this->memoryPeak;
    }
}