<?php
namespace PhpSandbox\Evaluator;

use EBernhardson\FastCGI\Client;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

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
     * @var string
     */
    private $phpVersion;

    /**
     * Evaluator constructor.
     * @param Config $config
     */
    public function __construct(Config $config, $phpVersion = null)
    {
        $this->config = $config;
        $this->setPHP($phpVersion);
    }

    /**
     * @param string $code
     * @return string
     */
    public function evaluate(string $code): string
    {
        $dir = $this->config->read('tmp_dir');

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $dir . self::FILENAME;

        $code = $this->insertBootstrap($code);
        $code = $this->insertBenchmarks($code);

        $fp = fopen($filename, 'wb');
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
    public function evaluateFile(string $filename): string
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException('File not found: ' . $filename);
        }

        $start = microtime(true);

        $client = new Client($this->phpVersion, '9000');

        $output = $client->request([
            'REQUEST_METHOD'  => 'GET',
            'SCRIPT_FILENAME' => $filename,
            'HTTP_ACCEPT'     => 'application/json',
        ], '');

        $this->time = microtime(true) - $start;

        return $this->parseOutput($output['body']);
    }

    /**
     * @param $code
     * @return string
     */
    private function insertBootstrap($code)
    {
        $bootstrapFile = $this->config->read('bootstrap_file');
        if (!strpos($code, $this->requireBootstrapString()) && !empty($bootstrapFile) && file_exists($bootstrapFile)) {
            $code = preg_replace('/^<\?php(\s*declare[^;]+;)?/', '<?php$1' . $this->requireBootstrapString(), $code);
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
    public function getLastCode(): string
    {
        $file = file_get_contents($this->config->read('tmp_dir') . self::FILENAME);
        $contents = explode(PHP_EOL, $file);

        foreach ($contents as $key => $value) {
            if (strpos($value, $this->requireBootstrapString())) {
                $contents[$key] = str_replace($this->requireBootstrapString(), '', $value);
                break;
            }
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

    /**
     * @param string $version
     */
    private function setPHP($version)
    {
        $phpVersions = $this->config->read('fast_cgi_hosts');
        if (is_array($phpVersions) && array_key_exists($version, $phpVersions)) {
            $this->phpVersion = $phpVersions[$version];
        }
    }
}