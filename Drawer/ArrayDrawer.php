<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 22.08.2017
 * Time: 21:40
 */

namespace Drawer;


class ArrayDrawer extends Drawer
{
    private $map;

    public function setMap(array $map)
    {
        $this->map = $map;
    }

    public function draw()
    {
        foreach ($this->map as $rows) {
            foreach ($rows as $cell) {
                echo $cell;
            }
            echo PHP_EOL;
        }
    }
}