<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:22
 */

namespace Tree;

abstract class Component
{
    abstract public function countItems($number);
    
    abstract public function countConstructTime($number, $parent);

    abstract protected function getName();
    
    public function addItems()
    {
        throw new \Exception('unsupported method');
    }
}