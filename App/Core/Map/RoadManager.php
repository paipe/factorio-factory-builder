<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.01.18
 * Time: 18:10
 */

declare(strict_types=1);

namespace App\Core\Map;


use App\Core\Map\Map;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Logger;
use App\Core\Utils\PathFinder\PathFinder;
use App\Core\Utils\Utils;

/**
 * Класс для управления дорогами
 *
 * Class RoadManager
 * @package App\Core\Map\Map
 */
class RoadManager
{

    /**
     * Находит финишный (последний) объект дороги в результате работы PathFinder'a
     *
     * @param Map $road
     * @return RoadObject
     *
     * @throws \Exception
     */
    public function getGoalRoadObject(Map $road): RoadObject
    {
        $firstRoad = $road->iterateMapObjects()->current();
        if (!($firstRoad instanceof RoadObject)) {
            throw new \Exception('Найденный элемент не является дорогой! Что-то пошло не так!');
        }
        while(!is_null($firstRoad->getNextObject())) {
            $firstRoad = $firstRoad->getNextObject();
        }

        return $firstRoad;
    }

    /**
     * Находит первый (стартовый) объект дороги в результате работы PathFinder'a
     *
     * @param Map $road
     * @return RoadObject
     *
     * @throws \Exception
     */
    public function getStartRoadObject(Map $road): RoadObject
    {
        $lastRoad = $road->iterateMapObjects()->current();
        if (!($lastRoad instanceof RoadObject)) {
            throw new \Exception('Найденный элемент не является дорогой! Что-то пошло не так!');
        }
        while(!is_null($lastRoad->getPrevObject())) {
            $lastRoad = $lastRoad->getPrevObject();
        }

        return $lastRoad;
    }

    /**
     * Пытается соединить две дороги, возвращает true|false
     * в зависимости от результата
     *
     * @param RoadObject $firstRoad
     * @param RoadObject $secondRoad
     * @param string $type
     * @return bool
     */
    public function connectRoads(RoadObject $firstRoad, RoadObject $secondRoad, string $type): bool
    {
        $result = false;
        if (
            $firstRoad->getLeftSide() === $secondRoad->getLeftSide() &&
            $firstRoad->getRightSide() === $secondRoad->getRightSide()
        ) {
            if ($firstRoad->isEmptyPrevRoad() && $secondRoad->isEmptyNextRoad() && $type === RoadObject::T_ROAD_START) {
                $firstRoad->setPrevObject($secondRoad);
                $secondRoad->setNextObject($firstRoad);
                $secondRoad->clearPointType();
                $result = true;
            } elseif ($firstRoad->isEmptyNextRoad() && $secondRoad->isEmptyPrevRoad() && $type === RoadObject::T_ROAD_GOAL) {
                $firstRoad->setNextObject($secondRoad);
                $secondRoad->setPrevObject($firstRoad);
                $secondRoad->clearPointType();
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Прокладывает дорогу из точки А в точку Б
     *
     * @param \App\Core\Map\Map $map
     * @param RoadObject $start
     * @param RoadObject $goal
     * @return \App\Core\Map\Map|null
     */
    public function findPath(Map $map, RoadObject $start, RoadObject $goal): ?Map
    {
        //клонируем карту и удаляем пункт назначения, чтобы PathFinder
        //корректно нашел дорогу без применения костылей в нем самом
        $result = null;
        $searchMap = clone $map;
        $searchMap->removeObject($goal->getCoordinates());
        $roadProto = new RoadObject(Utils::c(0, 0));
        $pathFinder = new PathFinder($searchMap, $roadProto);
        try {
            $road = $pathFinder->run(
                $start->getCoordinates(),
                $goal->getCoordinates()
            );
            $lastRoad = $this->getStartRoadObject($road);
            $firstRoad = $this->getGoalRoadObject($road);
            $lastRoad->getNextObject()->clearPrevObject();
            $firstRoad->getPrevObject()->clearNextObject();
            $road->removeObject($lastRoad->getCoordinates());
            $road->removeObject($firstRoad->getCoordinates());

            $someRoad = $this->getGoalRoadObject($road);
            do {
                $someRoad->setLeftSide($start->getLeftSide());
                $someRoad->setRightSide($start->getRightSide());
                $someRoad = $someRoad->getPrevObject();
            } while (!is_null($someRoad));

            $result = $road;
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * Строит сепаратор на карте
     *
     * @param Map $map
     * @param array $roadPoints
     *
     * @return array
     * @throws \Exception
     */
    public function buildSeparator(Map $map, array $roadPoints): array
    {
        if (count($roadPoints) !== 3) {
            throw new \Exception('Какой-то не запланированный сценарий!');
        }

        


    }

    public function processRoadDirections(Map $map)
    {
        foreach ($map->iterateMapObjects() as $object) {
            if ($object instanceof RoadObject && is_null($object->getDirection())) {
                $road = $object;
                while (!is_null($road->getPrevObject())) {
                    $road = $road->getPrevObject();
                }

                $directions = [
                    '10' => 'down',
                    '-10' => 'up',
                    '01' => 'right',
                    '0-1' => 'left'
                ];

                $prevDirection = NULL;
                do {
                    if (is_null($road->getPrevObject())) {
                        if (!is_null($road->getNextObject())) {
                            $road = $road->getNextObject();
                        } else {
                            $prevRoad = $road;
                            break;
                        }
                        continue;
                    }

                    $key = (string)($road->getY() - $road->getPrevObject()->getY()) .
                        (string)($road->getX() - $road->getPrevObject()->getX());
                    $direction = $directions[$key];

                    if (is_null($prevDirection)) {
                        $prevDirection = $direction;
                    }
                    if ($direction === $prevDirection) {
                        $roadType = $direction;
                    } else {
                        $roadType = $prevDirection . '_' . $direction;
                    }

                    $road->getPrevObject()->setDirection($roadType);
                    $prevDirection = $direction;
                    $prevRoad = $road;
                    $road = $road->getNextObject();
                } while (!is_null($road));
                $prevRoad->setDirection('left');
            }
        }
    }

}