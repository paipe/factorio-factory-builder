<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:08
 */

namespace Builder;


use Tree\Component;

abstract class Builder
{
    /**
     * @var Component
     */
    protected $tree;

    /**
     * @var int per second
     */
    protected $count;

    /**
     * @param Component $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    abstract public function build();

}