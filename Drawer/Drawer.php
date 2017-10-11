<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 18:40
 */

namespace Drawer;


abstract class Drawer
{
    
    abstract public function draw(array $map, array $schema);
    
}
