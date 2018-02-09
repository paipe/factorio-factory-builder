<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\MapManager;
use App\Core\Map\Objects\RoadObject;
use App\Core\Map\RoadManager;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;

class Planner
{

    /** Расстоение между мини-схемами */
    const DISTANCE = 4;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Drawer
     */
    protected $drawer;

    /**
     * @var Map
     */
    protected $resultMap;

    protected $mapManager;

    protected $roadManager;

    public function __construct()
    {
        $this->roadManager = new RoadManager();
        $this->mapManager = new MapManager();
    }

    /**
     * @param Map[] $buildObjects
     * @return Map
     */
    public function plan(array $buildObjects): Map
    {
        $this->resultMap = new Map();
        $x = 0;
        $y = 0;
        foreach ($buildObjects as $object) {
            $this->mapManager->mergeMaps($this->resultMap, $object, Utils::c($x, $y));
            Logger::info('Object merged to map.', [
                'height' => $object->getHeight(),
                'width'  => $object->getWidth(),
                'x' => $x,
                'y' => $y
            ]);
            //строим пока что все в линию слева на право, так что просто инкрементим X
            $x += $object->getWidth() + self::DISTANCE;
        }

        $this->buildRoads();
        $roadManager = new RoadManager();
        $roadManager->processRoadDirections($this->resultMap);

        return $this->resultMap;
    }

    protected function buildRoads(): void
    {
        $combinations = $this->resultMap->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            /**
             * @var RoadObject $entryPoint
             * @var RoadObject $exitPoint
             */
            $entryPoint = $combination['entry'];
            $exitPoint  = $combination['exit'];
            $road = $this->roadManager->findPath(
                $this->resultMap,
                $entryPoint,
                $exitPoint
            );
            try {
                $this->mapManager->mergeRoadToMap(
                    $this->resultMap,
                    $road,
                    Utils::c(0, 0)
                );
            } catch (\Error $e) {
                echo 'Кривой мердж карт' . PHP_EOL;
            }
        }
    }

}
