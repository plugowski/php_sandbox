<?php
namespace PhpSandbox\Snippet;

/**
 * Class SnippetRepository
 * @package PhpSandbox\Snippet
 */
class SnippetRepository
{
    /**
     * @var string
     */
    private $dir;

    /**
     * SnippetRepository constructor.
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
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
     * @param string $name
     * @return string
     */
    public function getSnippetContent($name)
    {
        $fullName = $this->getSnippetsDir() . $name;

        if (file_exists($fullName)) {
            return file_get_contents($fullName);
        }
        return '';
    }

    /**
     * Return list of all snippets placed in snippets directory.
     * @return SnippetCollection
     */
    public function getList()
    {
        return $this->recursiveScan($this->dir);
    }

    /**
     * Recursive scan selected folder and list all files and folders
     * @param string $dir
     * @return SnippetCollection
     */
    private function recursiveScan($dir)
    {
        $files = scandir($dir);
        $collection = new SnippetCollection();

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                $collection->add(new Snippet($file, Snippet::TYPE_FOLDER, $this->recursiveScan($dir . DIRECTORY_SEPARATOR . $file)));
            } else {
                $collection->add(new Snippet($file, Snippet::TYPE_FILE, new SnippetCollection()));
            }
        }

        return $collection;
    }

    /**
     * @param string|null $subFolder
     * @return string
     */
    private function getSnippetsDir($subFolder = null)
    {
        $dir = $this->dir;

        if (!is_null($subFolder)) {
            $dir .= DIRECTORY_SEPARATOR . $subFolder;
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * @param string $filename
     * @return bool
     * @throws SnippetException
     */
    public function delete($filename)
    {
        $fullName = $this->getSnippetsDir() . $filename;

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
        if ($dir !== $this->dir && count(scandir($dir)) == 2) {
            rmdir($dir);
        }

        return true;
    }
}