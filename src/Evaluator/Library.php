<?php
namespace PhpSandbox\Evaluator;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Library
 * @package Evaluator
 */
class Library
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Library constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function validatePackage($name)
    {
        $packageInfo = $this->getPackageInfo($name);
        return !empty($packageInfo);
    }

    /**
     * @param string $name
     */
    public function addPackage($name)
    {
        echo $this->composerCommand('require', ['packages' => [$name]]); //, '--quiet']);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function removePackage($name)
    {
        echo $this->composerCommand('remove', ['packages' => [$name]]);
        return true;
    }

    /**
     * @param string $name
     */
    public function showPackage($name)
    {
        $info = $this->getPackageInfo($name);

    }

    /**
     * @return array
     */
    public function getList()
    {
        if (!file_exists($this->config->read('vendors_dir') . '/../composer.json')) {
            return [];
        }

        $libs = ['composer' => []];
        $data = json_decode(file_get_contents($this->config->read('vendors_dir') . '/../composer.json'));
        $libs['composer'] = (array)$data->require;

        return $libs;
    }

    /**
     * @param string $name
     * @return array
     */
    private function getPackageInfo($name)
    {
        $info = $this->composerCommand('show', ['package' => $name]);
        preg_match_all('/(?<header>\w+)(?:\s*:)(\s+|\s(?<value>.+))\r?\n/', $info, $return);
        return array_combine($return['header'], $return['value']);
    }

    /**
     * @param string $command
     * @param array $params
     * @return string
     */
    private function composerCommand($command, $params = [])
    {
        if (!file_exists($this->config->read('vendors_dir'))) {
            mkdir($this->config->read('vendors_dir'), 0755, true);
        }

        putenv('COMPOSER=' . $this->config->read('vendors_dir') . '/../composer.json');
        putenv('COMPOSER_VENDOR_DIR=' . $this->config->read('vendors_dir'));
        putenv('COMPOSER_HOME=' . $this->config->read('tmp_dir'));

        $arrayInput = ['command' => $command] + $params;

        $input = new ArrayInput($arrayInput);
        $output = new BufferedOutput();

        $application = new Application();
        $application->setAutoExit(false);
        $application->run($input, $output);

        return $output->fetch();
    }
}