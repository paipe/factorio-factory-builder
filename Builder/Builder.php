<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:08
 */

declare(strict_types=1);

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

    public function setTree(Component $tree): Builder
    {
        $this->tree = $tree;
        return $this;
    }

    public function setCount(float $count): Builder
    {
        $this->count = $count;
        return $this;
    }

    abstract public function build(): array;

}