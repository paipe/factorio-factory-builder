<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30.01.18
 * Time: 16:58
 */

namespace App\Core\Builder;


use App\Core\Map;

abstract class BlueprintProto
{
    protected $blueprintMap;

    public function __construct()
    {
        $this->blueprintMap = new Map();
    }

    abstract public function make(array $object): Map;

}