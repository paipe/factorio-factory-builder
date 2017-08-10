<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 16:04
 */

namespace FactoryBlock;

use FactoryBlock\Works\GrayWorks;
use FactoryBlock\Works\Works;

class Factory
{
    /**
     * @var FactoryContext
     */
    private $context;

    /**
     * @var FactoryContainer
     */
    private $container;

    /**
     * @var Works[]
     */
    private $works = [];

    public function setContext(FactoryContext $context)
    {
        $this->context = $context;
    }

    public function setContainer(FactoryContainer $container)
    {
        $this->container = $container;
    }

    public function setFactoryElements()
    {
        $this->works[] = new GrayWorks();
    }

    public function build()
    {
        $data = $this->context->data;
        $count = $this->context->count;
        $rootName = $this->context->root;
        $root = $data[$rootName];
        $prod = GrayWorks::PRODUCTIVITY;
        $worksCount = $root['time'] * $count / $prod;

        return $worksCount;
    }
        

    
}
