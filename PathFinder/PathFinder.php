<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.10.17
 * Time: 11:36
 */

namespace PathFinder;


abstract class PathFinder
{
    abstract public function findPath($map, $start, $goal);
}