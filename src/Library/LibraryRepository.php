<?php
namespace PhpSandbox\Library;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class LibraryRepository
 * @package PhpSandbox\Library
 */
class LibraryRepository
{
    const PACKAGE_PATTERN = '/^[[:alnum:]-\/]+:[0-9\.]+$/';
    /**
     * @var string
     */
    private $vendorsDir;
    /**
     * @var string
     */
    private $tmpDir;

    /**
     * LibraryRepository constructor.
     * @param string $vendorsDir
     * @param string $tmpDir
     */
    public function __construct($vendorsDir, $tmpDir)
    {
        $this->vendorsDir = $vendorsDir;
        $this->tmpDir = $tmpDir;
    }

    /**
     * @param string $name
     * @return string
     */
    public function add($name)
    {
        return $this->composer('require', ['packages' => [$name]]); //, '--quiet']);
    }

    /**
     * @param string $name
     * @return string
     */
    public function delete($name)
    {
        return $this->composer('remove', ['packages' => [$name]]);
    }

    /**
     * @return LibraryCollection
     */
    public function getList()
    {
        $collection = new LibraryCollection();
        if (!file_exists($this->vendorsDir . '/../composer.json')) {
            return $collection;
        }

        $data = json_decode(file_get_contents($this->vendorsDir . '/../composer.json'));

        if (empty($data->require)) {
            return $collection;
        }

        foreach ($data->require as $name => $version) {
            $collection->add(new Library($name, $version));
        }

        return $collection;
    }

    /**
     * @param string $command
     * @param array $params
     * @return string
     */
    private function composer($command, $params = [])
    {
        if (!file_exists($this->vendorsDir)) {
            mkdir($this->vendorsDir, 0755, true);
        }

        putenv('COMPOSER=' . $this->vendorsDir . '/../composer.json');
        putenv('COMPOSER_VENDOR_DIR=' . $this->vendorsDir);
        putenv('COMPOSER_HOME=' . $this->tmpDir);

        $arrayInput = ['command' => $command] + $params;

        $input = new ArrayInput($arrayInput);
        $output = new BufferedOutput();

        $application = new Application();
        $application->setAutoExit(false);
        $application->run($input, $output);

        return $output->fetch();
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
}