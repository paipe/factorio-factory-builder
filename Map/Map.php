<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:23
 */

namespace Map;


use Map\Objects\EePointRoadObject;
use Map\Objects\RoadObject;
use Utils\Utils;

class Map
{
    private $grid = [];

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

    public function addRoadObject(RoadObject $object)
    {
        //todo: потестить, что возвращает класс, если его метод задан, но не установлен
        $result = false;
        $coordinateShifts = Utils::getPossibleCoordinatesShift($object->getCoordinates());
        foreach ($coordinateShifts as $shift) {
            $testObject = $this->getObjectByCoordinates($shift);
            if (
                isset($testObject) &&
                $testObject instanceof RoadObject &&
                $object->getLeftSide() === $testObject->getLeftSide() &&
                $object->getRightSide() === $testObject->getRightSide()
            ) {
                if ($object->isEmptyPrevRoad() && $testObject->isEmptyNextRoad()) {
                    $object->setPrevRoad($testObject);
                    $testObject->setNextRoad($object);
                    $result = true;
                } elseif ($object->isEmptyNextRoad() && $testObject->isEmptyPrevRoad()) {
                    $object->setNextRoad($testObject);
                    $testObject->setPrevRoad($object);
                    $result = true;
                }
            }
        }

        if ($result) {
            $this->addObject($object);
        } else {
            throw new \Exception('Не удалось добавить дорогу, условия не выполнены.');
        }
    }

    public function getObjectByCoordinates($coordinates): ?ObjectProto
    {
        return $this->grid[$coordinates['y']][$coordinates['x']] ?? null;
    }

    public function mergeMaps(Map $map, $coordinates)
    {
        $mapWidth = $map->getWidth();
        $mapHeight = $map->getHeight();
        for ($y = 0; $y < $mapHeight; $y++) {
            for ($x = 0; $x < $mapWidth; $x++) {
                $object = $map->getObjectByCoordinates(Utils::getCoords($x, $y));
                if (!is_null($object)) {
                    if (isset($this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x])) {
                        throw new \Exception('Кривой мердж карт!');
                    }
                    $this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x] = $object;
                }
            }
        }

        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if ($object instanceof RoadObject) {
                    $coordinateShifts = Utils::getPossibleCoordinatesShift($object->getCoordinates());
                    foreach ($coordinateShifts as $shift) {
                        $testObject = $this->getObjectByCoordinates($shift);
                        if (
                            isset($testObject) &&
                            $testObject instanceof RoadObject &&
                            $object->getLeftSide() === $testObject->getLeftSide() &&
                            $object->getRightSide() === $testObject->getRightSide()
                        ) {
                            if ($object->isEmptyPrevRoad() && $testObject->isEmptyNextRoad()) {
                                $object->setPrevRoad($testObject);
                                $testObject->setNextRoad($object);
                            } elseif ($object->isEmptyNextRoad() && $testObject->isEmptyPrevRoad()) {
                                $object->setNextRoad($testObject);
                                $testObject->setPrevRoad($object);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getEntryPoints()
    {
        $entryPoints = [];
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if (
                    $object instanceof EePointRoadObject &&
                    $object->getPointType() === EePointRoadObject::T_ENTRY
                ) {
                    $entryPoints[] = $object;
                }
            }
        }

        return $entryPoints;
    }

    public function getExitPoints()
    {
        $exitPoints = [];
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if (
                    $object instanceof EePointRoadObject &&
                    $object->getPointType() === EePointRoadObject::T_EXIT
                ) {
                    $exitPoints[] = $object;
                }
            }
        }

        return $exitPoints;
    }

    public function getStartEndRoadCombinations(): array
    {
        $result = [];
        /**
         * @var EePointRoadObject[] $entryPoints
         * @var EePointRoadObject[] $exitPoints
         */
        $entryPoints = $this->getEntryPoints();
        $exitPoints  = $this->getExitPoints();
        foreach($entryPoints as $entryPoint) {
            foreach ($exitPoints as $exitPoint) {
                if (
                    $entryPoint->getX() == $exitPoint->getX() &&
                    $entryPoint->getY() == $exitPoint->getY()
                ) {
                    $result[] = [
                        'entry' => $entryPoint,
                        'exit'  => $exitPoint
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

        return $width + 1;
    }

    public function getHeight(): int
    {
        $height = 0;
        foreach ($this->grid as $y => $row) {
            if ($y > $height) {
                $height = $y;
            }
        }

        return $height + 1;
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