<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 14:23
 */

namespace Planner;


use Builder\BuildObject;

class SimplePlanner extends Planner
{

    const DISTANCE = 10;
    
    public function plan()
    {
        $buildObjects = $this->builder->build();
        $map = $this->prepare($buildObjects);
    }

    /**
     * @param BuildObject[] $buildObjects
     * 
     * @return array
     */
    private function prepare($buildObjects)
    {
        
    }

}