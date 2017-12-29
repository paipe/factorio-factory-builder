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
    const DISTANCE = 10;

    /**
     * @var Map
     */
    protected $objectMap;

    /**
     * @param Map[] $buildObjects
     */
    public function plan($buildObjects)
    {
        $this->objectMap = new Map();
        $x = 0;
        $y = 0;
        foreach ($buildObjects as $object) {
            $this->objectMap->mergeMaps($object);
            $x += $object->getWidth() + self::DISTANCE;
        }

        $this->buildRoads();

    }

    protected function buildRoads()
    {
        $combinations = $this->objectMap->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            //ищем от конца до начала, чтобы потом не разворачивать массив
            $road = $this->pathFinder->findPath($this->objectMap, $combination['end'], $combination['start']);
            foreach ($road as $roadMap) {
                $this->objectMap->mergeMaps($roadMap);
            }
        }
    }

}