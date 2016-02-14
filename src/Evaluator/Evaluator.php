<?php
namespace PhpSandbox\Evaluator;

/**
 * Class Evaluator
 * @package PhpSandbox
 */
class Evaluator
{
    const FILENAME = 'code.php';
    const BENCHMARK_PATTERN = '/#benchmark#/';

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
     * @var Config
     */
    private $config;

    /**
     * Evaluator constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $code
     * @return string
     */
    public function evaluate($code)
    {
        $filename = $this->config->read('tmp_dir') . self::FILENAME;

        $code = $this->insertBootstrap($code);
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
            $this->config->read('php_command'),
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
    private function insertBootstrap($code)
    {
        $bootstrapFile = $this->config->read('bootstrap_file');
        if (!strpos($code, $this->requireBootstrapString()) && !empty($bootstrapFile) && file_exists($bootstrapFile)) {
            $code = str_replace('<?php', '<?php' . $this->requireBootstrapString(), $code);
        }

        return $code;
    }

    /**
     * @return string
     */
    private function requireBootstrapString()
    {
        return ' require \'' . $this->config->read('bootstrap_file') . '\'; ';
    }

    /**
     * @param $code
     * @return string
     */
    private function insertBenchmarks($code)
    {
        if ($this->config->has('benchmarks')) {
            $list = [];
            $insert = 'echo PHP_EOL . \'#benchmark#%s;';
            foreach ($this->config->read('benchmarks') as $name => $function) {
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
        if ($this->config->has('disable_functions')) {
            $cmd .= ' -d disable_functions=' . implode(',', $this->config->read('disable_functions'));
        }
        foreach ($this->config->read('directives') as $name => $value) {
            $cmd .= ' -d ' . $name . '=' . $value;
        }
        return $cmd;
    }

    /**
     * @return string
     */
    public function getLastCode()
    {
        $file = file_get_contents($this->config->read('tmp_dir') . self::FILENAME);
        $contents = explode(PHP_EOL, $file);

        if (strpos($contents[0], $this->requireBootstrapString())) {
            $contents[0] = str_replace($this->requireBootstrapString(), '', $contents[0]);
        }

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