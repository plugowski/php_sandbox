<?php
namespace PhpSandbox;

/**
 * Class Collection
 * @package PhpSandbox
 */
class Collection implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var object[]
     */
    private $items = [];

    /**
     * @param object $item
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        $return = [];
        foreach ($this->getIterator() as $object) {
            $return[] = $object;
        }
        return $return;
    }
}