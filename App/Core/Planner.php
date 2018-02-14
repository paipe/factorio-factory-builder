<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\Map;
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

    //TODO: тут нужно будет рефакторить
    protected function buildRoads(): void
    {
        $combinations = $this->resultMap->getRoadPointsCombinations();
        foreach ($combinations as $combination) {
            if (count($combination) === 2) {
                /** @var RoadObject $point */
                //TODO костыль
                foreach ($combination as $point) {
                    if ($point->getPointType() === RoadObject::T_ROAD_GOAL) {
                        $goalPoint = $point;
                    }
                    if ($point->getPointType() === RoadObject::T_ROAD_START) {
                        $startPoint = $point;
                    }
                }
                $road = $this->roadManager->findPath(
                    $this->resultMap,
                    $goalPoint,
                    $startPoint
                );
                $this->mapManager->mergeRoadToMap(
                    $this->resultMap,
                    $road,
                    Utils::c(0, 0)
                );
            } elseif (count($combination) === 3) {
                $count = [];
                /** @var RoadObject $point */
                foreach ($combination as $point) {
                    $count[] = $point->getPointType();
                }
                $data = array_count_values($count);
                $type = array_keys($data, max($data))[0];
                switch ($type) {
                    case RoadObject::T_ROAD_START:
                        throw new \Exception('Вариант с двумя выходами и одним входом пока не поддерживается!');
                        break;
                    case RoadObject::T_ROAD_GOAL:
                        //TODO костыль
                        foreach ($combination as $point) {
                            if ($point->getPointType() === RoadObject::T_ROAD_START) {
                                $startPoint = $point;
                                break;
                            }
                        }


                        break;
                }

            } else {
                throw new \Exception('Нестандартный размер пака комбинаций!');
            }
        }
    }

}
