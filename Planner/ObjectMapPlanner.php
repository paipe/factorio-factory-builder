<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

namespace Planner;


use Map\Map;

class objectMapPlanner extends Planner
{

    public function plan($buildObjects)
    {
        $this->map = new Map();

    }

}