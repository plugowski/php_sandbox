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

    /**
     * Validate name of created snippet
     *
     * @param string $name
     * @throws SnippetException
     */
    private function validateName($name)
    {
        if (substr_count($name, '/') > 1) {
            throw new SnippetException("Only one level of folders are allowed.", SnippetException::MAX_NESTING);
        }
        if (!preg_match('/^(\w+\/)?[\w\.]+$/', $name)) {
            throw new SnippetException("Name contains not allowed characters!", SnippetException::WRONG_NAME);
        }
        if (false === strpos($name, '.php')) {
            throw new SnippetException("Extension is required and should be *.php", SnippetException::MISSING_EXTENSION);
        }
    }

    /**
     * Save posted code into snippet
     *
     * @param string $name
     * @param string $code
     * @return bool
     */
    public function save($name, $code)
    {
        $this->validateName($name);

        $dir = null;
        if (false !== strpos($name, '/')) {
            list($dir, $name) = explode('/', $name);
        }

        $fh = fopen($this->getSnippetsDir($dir) . DIRECTORY_SEPARATOR . $name, 'w+');
        fwrite($fh, $code);
        fclose($fh);

        return true;
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
     * @throws SnippetException
     * @return bool
     */
    public function delete($params)
    {
        $fullName = $this->getSnippetsDir() . $params['filename'];

        if (!file_exists($fullName)) {
            throw new SnippetException("File does not exists!", SnippetException::FILE_NOT_EXISTS);
        }
        if (!is_writable($fullName)) {
            throw new SnippetException("No permission to modify file!", SnippetException::NO_PERMISSION);
        }

        unlink($fullName);

        $parts = explode('/', $fullName);
        array_pop($parts);
        $dir = implode('/', $parts);

        // if dir is empty, remove it
        if (count(scandir($dir)) == 2) {
            rmdir($dir);
        }

        return true;
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