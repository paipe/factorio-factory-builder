<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.01.18
 * Time: 18:09
 */

declare(strict_types=1);

namespace App\Core\Map;

use App\Core\Map;
use App\Core\Utils\Utils;
use App\Core\Map\Objects\RoadObject;

/**
 * Класс-надстройка для операций с картами
 *
 * Class MapManager
 * @package App\Core\Map
 */
class MapManager
{

    private $roadManager;

    public function __construct()
    {
        $this->roadManager = new RoadManager();
    }

    /**
     * Накладываем вторую карту на первую в указанных координатах
     *
     * @param Map $firstMap
     * @param Map $secondMap
     * @param array $coordinates
     *
     * @return Map
     */
    public function mergeMaps(Map $firstMap, Map $secondMap, $coordinates): Map
    {
        foreach ($secondMap->iterateMapObjects() as $object) {
            /** @var ObjectProto $object $x */
            $x = $coordinates['x'] + $object->getX();
            $y = $coordinates['y'] + $object->getY();
            $object->setX($x)->setY($y);
            $firstMap->addObject($object);
        }

        return $firstMap;
    }

    /**
     * Добавлям дорогу на карту и проверяем, нужно ли присоединять
     * ее к имеющимся на карте дорогам (как правило - нужно).
     * Вызывается после стройки дорог PathFinder'ом.
     *
     * @param Map $map
     * @param Map $road
     * @param array $coordinates
     *
     * @return Map
     */
    public function mergeRoadToMap(Map $map, Map $road, array $coordinates): Map
    {
        $roadManager = new RoadManager();
        $goalRoad = $roadManager->getGoalRoadObject($road);
        $startRoad = $roadManager->getStartRoadObject($road);
        $map = $this->mergeMaps($map, $road, $coordinates);

        $this->checkRoadNeighbourPoints($map, $goalRoad, RoadObject::T_ROAD_GOAL);
        $this->checkRoadNeighbourPoints($map, $startRoad, RoadObject::T_ROAD_START);

        return $map;
    }

    /**
     * Проверяет соседние для дороги клетки на возможность
     * соединения с другой дорогой.
     *
     * @param Map $map
     * @param RoadObject $roadObject
     * @param string $type
     * @return bool
     */
    private function checkRoadNeighbourPoints(Map $map, RoadObject $roadObject, string $type): bool
    {
        $result = false;
        $roadManager = new RoadManager();
        $coordinateShifts = Utils::getPossibleCoordinatesShift($roadObject->getCoordinates());
        while (!empty($coordinateShifts) && $result === false) {
            $coordinates = array_shift($coordinateShifts);
            $testObject = $map->getObjectByCoordinates($coordinates);
            if ($testObject instanceof RoadObject && $testObject->getPointType() === $type) {
                $result = $roadManager->connectRoads($roadObject, $testObject);
            }
        }

        return $result;
    }

}