<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.01.18
 * Time: 18:10
 */

declare(strict_types=1);

namespace App\Core\Map;


use App\Core\Map;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Logger;
use App\Core\Utils\PathFinder\PathFinder;
use App\Core\Utils\Utils;

/**
 * Класс для управления дорогами
 *
 * Class RoadManager
 * @package App\Core\Map
 */
class RoadManager
{

    /**
     * Находит первый объект дороги в результате работы PathFinder'a
     *
     * @param Map $road
     * @return RoadObject
     *
     * @throws \Exception
     */
    public function getFirstRoadObject(Map $road): RoadObject
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
     * Находит последний объект дороги в результате работы PathFinder'a
     *
     * @param Map $road
     * @return RoadObject
     *
     * @throws \Exception
     */
    public function getLastRoadObject(Map $road): RoadObject
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
     * @return bool
     */
    public function connectRoads(RoadObject $firstRoad, RoadObject $secondRoad): bool
    {
        $result = false;
        if (
            $firstRoad->getLeftSide() === $secondRoad->getLeftSide() &&
            $firstRoad->getRightSide() === $secondRoad->getRightSide()
        ) {
            if ($firstRoad->isEmptyPrevRoad() && $secondRoad->isEmptyNextRoad() && $secondRoad->getPointType() !== RoadObject::T_ENTRY) {
                $firstRoad->setPrevObject($secondRoad);
                $secondRoad->setNextObject($firstRoad);
                $result = true;
            } elseif ($firstRoad->isEmptyNextRoad() && $secondRoad->isEmptyPrevRoad() && $secondRoad->getPointType() !== RoadObject::T_EXIT) {
                $firstRoad->setNextObject($secondRoad);
                $secondRoad->setPrevObject($firstRoad);
                $result = true;
            }
        }

        return $result;
    }

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
            $lastRoad = $this->getLastRoadObject($road);
            $firstRoad = $this->getFirstRoadObject($road);
            $lastRoad->getNextObject()->clearPrevObject();
            $firstRoad->getPrevObject()->clearNextObject();
            $road->removeObject($lastRoad->getCoordinates());
            $road->removeObject($firstRoad->getCoordinates());

            $someRoad = $this->getFirstRoadObject($road);
            do {
                $someRoad->setLeftSide($start->getLeftSide());
                $someRoad->setRightSide($start->getRightSide());
                $someRoad = $someRoad->getNextObject();
            } while (!is_null($someRoad));

            $result = $road;
        } catch (\Exception $e) {
            Logger::notice(
                'Не удалось найти путь для дороги.',
                [$start->getCoordinates(), $goal->getCoordinates()]
            );
        }

        return $result;
    }

}