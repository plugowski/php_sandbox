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
     * @return bool
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
        if (!preg_match(LibraryRepository::PACKAGE_PATTERN, $name)) {
            return false;
        }

        $packageName = explode(':', $name)[0];
        $results = $this->repository->search($packageName);
        /** @var Library $package */
        foreach ($results as $package) {
            if ($packageName === $package->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return LibraryCollection
     */
    public function getList()
    {
        return $this->repository->getList();
    }
}