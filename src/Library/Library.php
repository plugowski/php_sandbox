<?php
namespace PhpSandbox\Library;

/**
 * Class Library
 * @package PhpSandbox\Library
 */
class Library implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $version;

    /**
     * Library constructor.
     * @param string $name
     * @param string $version
     */
    public function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'version' => $this->getVersion()
        ];
    }
}