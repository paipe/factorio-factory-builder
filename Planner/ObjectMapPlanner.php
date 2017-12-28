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

    /**
     * @param Map[] $buildObjects
     */
    public function plan($buildObjects)
    {
        $map = new Map();
        $x = 0;
        $y = 0;
        foreach ($buildObjects as $object) {
            $map->mergeMaps($object);
            $x += $object->getWidth() + self::DISTANCE;
        }

        $this->buildRoads($map);

    }

    /**
     * @param Map $map
     */
    protected function buildRoads($map)
    {
        $combinations = $map->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            $road = $this->pathFinder->findPath($map, $combination['start'], $combination['end']);
            //разворачиваем, т.к. в исхоном варианте массив идет от конца дороги к началу, что не очень удобно
            //TODO возможно легче поменять старт и энд в findPath?
            $road = array_reverse($road);
            foreach ($road as $key => $coordinates) {
                //TODO описать отрисовку дороги
            }
        }
    }

}