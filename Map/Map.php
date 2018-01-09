<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:23
 */

namespace Map;


use Map\Objects\RoadObject;
use Utils\Utils;

class Map
{
    private $grid = [];
    /**
     * @var Road[] array
     */
    private $roads = [];
    private $entryPoints = [];
    private $exitPoints = [];

    public function addObject(ObjectProto $object)
    {
        for ($y = 0; $y < $object->getHeight(); $y++) {
            for ($x = 0; $x < $object->getWidth(); $x++) {
                if (isset($this->grid[$object->getY() + $y][$object->getX() + $x])) {
                    throw new \Exception('Перезапись занятой клетки карты!');
                }
                $this->grid[$object->getY() + $y][$object->getX() + $x] = $object;
            }
        }
    }

    public function addRoadObject(RoadObject $object, int $roadIndex)
    {
        if (!isset($this->roads[$roadIndex])) {
            throw  new \Exception('Дороги с переданным индексом нет!');
        }

        $lastRoadObject = $this->roads[$roadIndex]->lastRoad();
        if ($lastRoadObject) {
            $lastX = $lastRoadObject->getX();
            $lastY = $lastRoadObject->getY();
            $x = $object->getX();
            $y = $object->getY();
            if (
                !(
                    (abs($x - $lastX) == 1 && $y == $lastY) ||
                    ($x == $lastX && abs($y - $lastY) == 1)
                )
            ) {
                throw new \Exception('Некорректное создание дороги!');
            }
        }

        $this->addObject($object);
        $this->roads[$roadIndex]->continuePath($object);
    }

    public function addRoad(Road $road)
    {
        $this->roads[] = $road;
        return $this->getLastRoadIndex();
    }

    public function getLastRoadIndex(): int
    {
        return count($this->roads) - 1;
    }

    public function getRoads()
    {
        return $this->roads;
    }

    public function getObjectByCoordinates($coordinates): ?ObjectProto
    {
        return $grid[$coordinates['y']][$coordinates['x']] ?? null;
    }

    public function mergeMaps(Map $map, $coordinates)
    {
        $mapWidth = $map->getWidth();
        $mapHeight = $map->getHeight();
        //пробуем соединять все дороги подряд
        //не самый грамотный вариант, зато не надо писать никаких условий
        //так как сравнение односторонее, нужно пробовать мерджить в обе стороны
        $mapRoads = $map->getRoads();
        foreach ($this->roads as $road) {
            foreach ($mapRoads as $mapRoad) {
                if (!$road->isRoadEmpty() && !$mapRoad->isRoadEmpty()) {
                    try {
                        $road->implodeRoads($mapRoad);
                    } catch (\Exception $e) {

                    }
                }
                if (!$road->isRoadEmpty() && !$mapRoad->isRoadEmpty()) {
                    try {
                        $mapRoad->implodeRoads($road);
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        foreach ($mapRoads as $road) {
            if (!$road->isRoadEmpty()) {
                $this->roads[] = $road;
            }
        }

        for ($y = 0; $y < $mapHeight; $y++) {
            for ($x = 0; $x < $mapWidth; $x++) {
                $object = $map->getObjectByCoordinates(Utils::getCoords($x, $y));
                if (!is_null($object)) {
                    if (
                        isset($this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x]) &&
                        !($object instanceof RoadObject)
                    ) {
                        throw new \Exception('Кривой мердж карт!');
                    }
                    $this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x] = $object;
                }
            }
        }

        $mapEntryPoints = $map->getEntryPoints();
        foreach ($mapEntryPoints as $coords => $entryPoint) {
            if (isset($this->entryPoints[$coords])) {
                continue;
                //throw new \Exception('Такой entryPoint уже есть!');
            }
            $this->entryPoints[$coords] = $entryPoint;
        }

        $mapExitPoints = $map->getExitPoints();
        foreach ($mapExitPoints as $coords => $exitPoint) {
            if (isset($this->exitPoints[$coords])) {
                continue;
//                throw new \Exception('Такой exitPoint уже есть!');
            }
            $this->exitPoints[$coords] = $exitPoint;
        }
    }

    public function addEntryPoint($productName, $coordinates)
    {
        $this->entryPoints[$coordinates['y'] . ':' . $coordinates['x']] = $productName;
    }

    public function addExitPoint($productName, $coordinates)
    {
        $this->exitPoints[$coordinates['y'] . ':' . $coordinates['x']] = $productName;
    }

    public function getEntryPoints()
    {
        return $this->entryPoints;
    }

    public function getExitPoints()
    {
        return $this->exitPoints;
    }

    public function getStartEndRoadCombinations(): array
    {
        $result = [];
        foreach ($this->entryPoints as $entryCoords => $entryProduct) {
            foreach ($this->exitPoints as $exitCoords => $exitProduct) {
                if ($entryProduct === $exitProduct) {
                    $result[] = [
                        'start' => $entryCoords,
                        'end'   => $exitCoords
                    ];
                }
            }
        }

        return $result;
    }

    public function iterateMapObjects()
    {
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $object) {
                /** @var ObjectProto $object */
                if ($object->getX() === $x && $object->getY() === $y) {
                    yield $object;
                }
            }
        }
    }

    //подразумеваем, что отрицательных координат у нас нет
    public function getWidth(): int
    {
        $width = 0;
        foreach ($this->grid as $row) {
            foreach ($row as $x => $object) {
                if ($x > $width) {
                    $width = $x;
                }
            }
        }

        return $width;
    }

    public function getHeight(): int
    {
        $height = 0;
        foreach ($this->grid as $y => $row) {
            if ($y > $height) {
                $height = $y;
            }
        }

        return $height;
    }

//    private function getMapSize(): array
//    {
//        if (empty($this->grid)) {
//            return [
//                'height' => 0,
//                'width' => 0
//            ];
//        }
//
//        $minX = PHP_INT_MAX;
//        $maxX = 0;
//        $minY = PHP_INT_MAX;
//        $maxY = 0;
//        foreach ($this->grid as $rowKey => $rows) {
//            if ($rowKey > $maxY) $maxY = $rowKey;
//            if ($rowKey < $minY) $minY = $rowKey;
//            foreach ($rows as $pointKey => $point) {
//                if ($pointKey > $maxX) $maxX = $pointKey;
//                if ($pointKey < $minX) $minX = $pointKey;
//            }
//        }
//
//        return [
//            'width'  => abs($minX) + abs($maxX),
//            'height' => abs($minY) + abs($maxY)
//        ];
//    }
}