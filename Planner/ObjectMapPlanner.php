<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

namespace Planner;


use Map\Map;
use Map\Objects\EePointRoadObject;
use Utils\Logger;
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
            Logger::info('Object merged to map.', [
                'height' => $object->getHeight(),
                'width'  => $object->getWidth(),
                'x' => $x,
                'y' => $y
            ]);
            //строим пока что все в линию слева на право, так что просто инкрементим X
            $x += $object->getWidth() + self::DISTANCE;
        }

//        $this->buildRoads();
        $this->newBuildRoads();

        return $this->objectMap;
    }

    protected function buildRoads()
    {
        $combinations = $this->objectMap->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            /**
             * @var EePointRoadObject $entryPoint
             * @var EePointRoadObject $exitPoint
             */
            $entryPoint = $combination['entry'];
            $exitPoint  = $combination['exit'];
            //ищем от конца до начала, чтобы потом не разворачивать массив
            $road = $this->pathFinder->findPath(
                $this->objectMap,
                $entryPoint,
                $exitPoint
            );
            try {
                $this->objectMap->mergeMaps(
                    $road,
                    Utils::getCoords(0, 0)
                );
            } catch (\Error $e) {
                echo 'Кривой мердж карт' . PHP_EOL;
            }
        }
    }

    protected function newBuildRoads()
    {
        $pointGroups = $this->objectMap->getRoadCombinationsGroupedByProduct();
        foreach ($pointGroups as $productName => $group) {
            if (count($group) === 1) {
                continue;
            } elseif (count($group) === 2) {
                $this->simpleBuildRoad($group);
            } else {
                $this->magickBuildRoad($group);
            }
        }

    }

    /**
     * @param EePointRoadObject[] $group
     */
    protected function simpleBuildRoad($group)
    {
        foreach ($group as $object) {
            if ($object->getPointType() === EePointRoadObject::T_EXIT) {
                $exitPoint = $object;
            }
            if ($object->getPointType() === EePointRoadObject::T_ENTRY) {
                $entryPoint = $object;
            }
        }
        $road = $this->pathFinder->findPath(
            $this->objectMap,
            $entryPoint,
            $exitPoint
        );
        try {
            $this->objectMap->mergeMaps(
                $road,
                Utils::getCoords(0, 0)
            );
        } catch (\Error $e) {
            echo 'Кривой мердж карт' . PHP_EOL;
        }

        Logger::info('Simple road was built.', [
            'start' => 'x: ' . $entryPoint->getX() . ', y: ' . $entryPoint->getY(),
            'end'   => 'x: ' . $exitPoint->getX() . ', y: ' . $exitPoint->getY(),
            'product' => $entryPoint->getPointProduct()
        ]);
    }

    /**
     * @param EePointRoadObject[] $group
     */
    private function magickBuildRoad($group)
    {
        //todo ветвление и все такое будут тут
    }

}
