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
        while(!is_null($firstRoad->getNextRoad())) {
            $firstRoad = $firstRoad->getNextRoad();
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
        while(!is_null($lastRoad->getPrevRoad())) {
            $lastRoad = $lastRoad->getPrevRoad();
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
            if ($firstRoad->isEmptyPrevRoad() && $secondRoad->isEmptyNextRoad()) {
                $firstRoad->setPrevRoad($secondRoad);
                $secondRoad->setNextRoad($firstRoad);
                $result = true;
            } elseif ($firstRoad->isEmptyNextRoad() && $secondRoad->isEmptyPrevRoad()) {
                $firstRoad->setNextRoad($secondRoad);
                $secondRoad->setPrevRoad($firstRoad);
                $result = true;
            }
        }

        return $result;
    }

}