<?php


namespace App\App\Tasks;


use App\Base\Module;

abstract class AbstractTask extends Module
{

    public function prepare() {}

    abstract public function run();

}