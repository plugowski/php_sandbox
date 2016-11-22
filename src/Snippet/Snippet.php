<?php
namespace PhpSandbox\Snippet;

/**
 * Class Snippet
 * @package Evaluator
 */
class Snippet implements \JsonSerializable
{
    const TYPE_FILE = 'file';
    const TYPE_FOLDER = 'folder';
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var SnippetCollection
     */
    private $children;

    /**
     * Snippet constructor.
     * @param string $name
     * @param string $type
     * @param SnippetCollection $children
     */
    public function __construct($name, $type = self::TYPE_FILE, SnippetCollection $children = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->children = $children;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return SnippetCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'data' => $this->getChildren()
        ];
    }
}