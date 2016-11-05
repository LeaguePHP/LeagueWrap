<?php

namespace LeagueWrap\Dto;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LeagueWrap\Exception\ListKeyNotSetException;

abstract class AbstractListDto extends AbstractDto implements ArrayAccess, IteratorAggregate, Countable
{
    protected $listKey = null;

    /**
     * Check if offset exists.
     *
     * @param mixed $offset
     *
     * @throws ListKeyNotSetException
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $info = $this->getListByKey();

        return isset($info[$offset]);
    }

    /**
     * Get the value at the given offset.
     *
     * @param mixed $offset
     *
     * @throws ListKeyNotSetException
     *
     * @return null
     */
    public function offsetGet($offset)
    {
        $info = $this->getListByKey();
        if (!isset($info[$offset])) {
            return;
        }

        return $info[$offset];
    }

    /**
     * Set a value at the given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws ListKeyNotSetException
     */
    public function offsetSet($offset, $value)
    {
        // just to make sure the listKey exists
        $this->getListByKey();
        if (is_null($offset)) {
            $this->info[$this->listKey][] = $value;
        } else {
            $this->info[$this->listKey][$offset] = $value;
        }
    }

    /**
     * Remove a value at a given offset.
     *
     * @param mixed $offset
     *
     * @throws ListKeyNotSetException
     */
    public function offsetUnset($offset)
    {
        $this->getListByKey();
        unset($this->info[$this->listKey][$offset]);
    }

    /**
     * Returns the array iterator.
     *
     * @throws ListKeyNotSetException
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getListByKey());
    }

    /**
     * Returns the count of the size of the list.
     *
     * @throws ListKeyNotSetException
     *
     * @return int
     */
    public function count()
    {
        return count($this->getListByKey());
    }

    /**
     * Returns the list by key.
     *
     * @throws ListKeyNotSetException
     *
     * @return mixed
     */
    protected function getListByKey()
    {
        if ($this->listKey == '') {
            return $this->info;
        }

        if (is_null($this->listKey) ||
            !isset($this->info[$this->listKey])
        ) {
            throw new ListKeyNotSetException('The listKey is not found in the abstract list DTO');
        }

        return $this->info[$this->listKey];
    }
}
