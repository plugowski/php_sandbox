<?php
namespace PhpSandbox\Library;

/**
 * Class Library
 * @package Evaluator
 */
class LibraryService
{
    /**
     * @var LibraryRepository
     */
    private $repository;

    /**
     * Library constructor.
     * @param LibraryRepository $repository
     * @internal param Config $config
     */
    public function __construct(LibraryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $name
     */
    public function addPackage($name)
    {
        return $this->repository->add($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function removePackage($name)
    {
        echo $this->repository->delete($name);
        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function validatePackage($name)
    {
        return preg_match(LibraryRepository::PACKAGE_PATTERN, $name);
    }

    /**
     * @return LibraryCollection
     */
    public function getList()
    {
        return $this->repository->getList();
    }
}