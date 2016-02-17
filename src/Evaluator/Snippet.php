<?php
namespace PhpSandbox\Evaluator;

/**
 * Class Snippet
 * @package Evaluator
 */
class Snippet
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Snippet constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string|null $subFolder
     * @return string
     */
    private function getSnippetsDir($subFolder = null)
    {
        $dir = $this->config->read('snippets_dir');

        if (!is_null($subFolder)) {
            $dir .= DIRECTORY_SEPARATOR . $subFolder;
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function validateName()
    {

    }

    /**
     * Save posted code into snippet
     */
    public function save()
    {
        $name = explode('/', $_POST['name']);
        $counter = count($name);

        if ($counter > 2) {
            echo 'maksymalnie jedno zagniezdzenie!';
            die;
        } else if ($counter == 2) {
            $dir = $this->getSnippetsDir($name[0]) . DIRECTORY_SEPARATOR;
            $filename = $name[1];
        } else {
            $dir = $this->getSnippetsDir() . DIRECTORY_SEPARATOR;
            $filename = $name[0];
        }

        $fh = fopen($dir . $filename, 'w+');
        fwrite($fh, $_POST['code']);
        fclose($fh);

        // todo: return status
    }

    /**
     * Return snippet content
     *
     * @param array $params
     */
    public function load($params)
    {
        $fullName = $this->getSnippetsDir() . $params['filename'];

        if (file_exists($fullName)) {
            $code = file_get_contents($fullName);
        }

        echo json_encode(compact('code'));
    }

    /**
     * Delete snippet
     *
     * @param array $params
     * @return bool
     */
    public function delete($params)
    {
        $fullName = $this->getSnippetsDir() . $params['filename'];
        if (file_exists($fullName) && is_writable($fullName)) {
            unlink($fullName);
        }
        // todo: return status
    }

    /**
     * Return list of all snippets
     *
     * @return array
     */
    public function getList()
    {
        $files = $this->recursiveScan($this->getSnippetsDir());
        return $this->prepareArrayToJson($files);
    }

    /**
     * Build JSON with structure of folders and files
     *
     * @param $files
     * @return array
     */
    private function prepareArrayToJson($files)
    {
        $tmp = [];
        foreach ($files as $k => $value) {

            $name = $value;
            $type = 'file';
            $data = [];

            if (preg_match('/^>\s/', $k)) {
                $type = 'folder';
                $name = str_replace('> ', '', $k);
                $data = $this->prepareArrayToJson($value);
            }

            $tmp[] = ['name' => $name, 'type' => $type, 'data' => $data];
        }
        return $tmp;
    }

    /**
     * Recursive scan selected folder and list all files and folders
     *
     * @param string $dir
     * @return array
     */
    private function recursiveScan($dir)
    {
        $files = scandir($dir);
        $tmp = [];

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                $tmp['> ' . $file] = $this->recursiveScan($dir . DIRECTORY_SEPARATOR . $file);
            } else {
                $tmp[] = $file;
            }
        }
        ksort($tmp);
        return $tmp;
    }
}