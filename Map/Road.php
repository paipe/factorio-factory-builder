<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 13:43
 */

namespace Map;


use Map\Objects\RoadObject;

class Road
{
    /**
     * набор RoadObject[]'ов, которые принадлежат данной дороге
     */
    private $path;

    /**
     * имя продукта, который занимает ту или иную сторону дороги
     */
    private $leftSide;
    private $rightSide;

    public function implodeRoads(Road $road)
    {

    }

    public function explodeRoad(): array
    {

    }

    public function continuePath(RoadObject $object)
    {
        $this->path = $object;
    }


}