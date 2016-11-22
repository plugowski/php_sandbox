<?php
namespace PhpSandbox\Snippet;

/**
 * Class SnippetService
 * @package PhpSandbox\Snippet
 */
class SnippetService
{
    /**
     * @var SnippetRepository
     */
    private $repository;

    /**
     * Snippet constructor.
     * @param SnippetRepository $repository
     */
    public function __construct(SnippetRepository $repository)
    {
        $this->repository = $repository;
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
     * @return SnippetCollection
     */
    public function getList()
    {
        return $this->repository->getList();
    }

    /**
     * Save posted code as snippet
     * @param string $name
     * @param string $code
     * @return bool
     */
    public function save($name, $code)
    {
        $this->validateName($name);
        return $this->repository->save($name, $code);
    }

    /**
     * Return snippet content
     * @param string $filename
     * @return array
     */
    public function load($filename)
    {
        return ['code' => $this->repository->getSnippetContent($filename)];
    }

    /**
     * Delete snippet
     * @param string$filename
     * @return bool
     */
    public function delete($filename)
    {
        return $this->repository->delete($filename);
    }
}