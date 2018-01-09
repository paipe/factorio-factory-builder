<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

namespace Planner;


use Map\Map;
use Utils\Utils;

class ObjectMapPlanner extends Planner
{
    const DISTANCE = 10;

    /**
     * @var Map
     */
    protected $objectMap;

    /**
     * @param Map[] $buildObjects
     * @return Map
     */
    public function plan($buildObjects)
    {
        $this->objectMap = new Map();
        $x = 0;
        $y = 0;
        foreach ($buildObjects as $object) {
            $this->objectMap->mergeMaps(
                $object,
                Utils::getCoords($x, $y)
            );
            //строим пока что все в линию слева на право, так что просто инкрементим X
            $x += $object->getWidth() + self::DISTANCE;
        }

        $this->buildRoads();

        return $this->objectMap;
    }

    protected function buildRoads()
    {
        $combinations = $this->objectMap->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            //ищем от конца до начала, чтобы потом не разворачивать массив
            $road = $this->pathFinder->findPath(
                $this->objectMap,
                Utils::getCoords($combination['end'][0], $combination['end'][2]),
                Utils::getCoords($combination['start'][0], $combination['start'][2])
            );
            foreach ($road as $roadMap) {
                $this->objectMap->mergeMaps(
                    $roadMap,
                    Utils::getCoords(0, 0)
                );
            }
        }
    }

}