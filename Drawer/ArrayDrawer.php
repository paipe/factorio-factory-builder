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
    public function draw(array $data, array $schema)
    {
        foreach ($data as $rows) {
            foreach ($rows as $cell) {
                echo $cell;
            }
            echo PHP_EOL;
        }
    }
}