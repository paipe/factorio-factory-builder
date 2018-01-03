<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 02.01.18
 * Time: 14:34
 */

namespace Utils;


class Stack
{

    private $stack = [];

    public function push(mixed $value): int
    {
        return array_unshift($this->stack, $value);
    }

    public function pop(): mixed
    {
        return array_shift($this->stack);
    }

    public function top(): mixed
    {
        return current($this->stack);
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }

}