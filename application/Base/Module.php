<?php


namespace App\Base;


abstract class Module
{
    private $container;
    private $context;

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }


}