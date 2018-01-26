<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:23
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\ObjectProto;
use App\Core\Map\Objects\EePointRoadObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Utils;

class Map
{
    private $grid = [];

    public function addObject(ObjectProto $object): void
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

    public function addRoadObject(RoadObject $object): void
    {
        //todo: потестить, что возвращает класс, если его метод задан, но не установлен
        $result = false;
        $coordinateShifts = Utils::getPossibleCoordinatesShift($object->getCoordinates());
        $countEmpty = 0;
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
                    break;
                } elseif ($object->isEmptyNextRoad() && $testObject->isEmptyPrevRoad()) {
                    $object->setNextRoad($testObject);
                    $testObject->setPrevRoad($object);
                    $result = true;
                    break;
                }

            } else {
                if (++$countEmpty === 4) {
                    $result = true;
                }
            }
        }

        if ($result) {
            $this->addObject($object);
        } else {
            throw new \Exception('Не удалось добавить дорогу, условия не выполнены.' . $object->getX() . '_' . $object->getY());
        }
    }

    public function getObjectByCoordinates(array $coordinates): ?ObjectProto
    {
        return $this->grid[$coordinates['y']][$coordinates['x']] ?? null;
    }

    public function mergeMaps(Map $map, array $coordinates): void
    {
        $mapWidth = $map->getWidth();
        $mapHeight = $map->getHeight();
        for ($y = 0; $y < $mapHeight; $y++) {
            for ($x = 0; $x < $mapWidth; $x++) {
                $object = $map->getObjectByCoordinates(Utils::c($x, $y));
                if (!is_null($object)) {
                    if (isset($this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x])) {
                        if (
                            $object instanceof RoadObject &&
                            $this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x] instanceof RoadObject
                        ) {
                            /** @var RoadObject $mapRoad */
                            $mapRoad = $this->grid[$coordinates['y'] + $y][$coordinates['x'] + $x];
                            if (is_null($mapRoad->getNextRoad()) && !is_null($object->getNextRoad())) {
                                $mapRoad->setNextRoad($object->getNextRoad());
                                $object->getNextRoad()->setPrevRoad($mapRoad, true);
                            } elseif (is_null($mapRoad->getPrevRoad()) && !is_null($object->getPrevRoad())) {
                                $mapRoad->setPrevRoad($object->getPrevRoad());
                                $object->getPrevRoad()->setNextRoad($mapRoad, true);
                            }
                            continue;
                        } else {
                            throw new \Exception('Кривой мердж карт!');
                        }
                    }
                    if ($object->getX() === $x && $object->getY() === $y) {
                        $object->setX($coordinates['x'] + $x);
                        $object->setY($coordinates['y'] + $y);
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

    public function processRoadDirections(): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if ($object instanceof RoadObject && is_null($object->getDirection())) {
                    $road = $object;
                    while (!is_null($road->getPrevRoad())) {
                        $road = $road->getPrevRoad();
                    }

                    $directions = [
                        '10' => 'down',
                        '-10' => 'up',
                        '01' => 'right',
                        '0-1' => 'left'
                    ];

                    $prevDirection = NULL;
                    do {
                        if (is_null($road->getPrevRoad())) {
                            if (!is_null($road->getNextRoad())) {
                                $road = $road->getNextRoad();
                            } else {
                                $prevRoad = $road;
                                break;
                            }
                            continue;
                        }

                        $key = (string)($road->getY() - $road->getPrevRoad()->getY()) .
                            (string)($road->getX() - $road->getPrevRoad()->getX());
                        $direction = $directions[$key];

                        if (is_null($prevDirection)) {
                            $prevDirection = $direction;
                        }
                        if ($direction === $prevDirection) {
                            $roadType = $direction;
                        } else {
                            $roadType = $prevDirection . '_' . $direction;
                        }

                        $road->getPrevRoad()->setDirection($roadType);
                        $prevDirection = $direction;
                        $prevRoad = $road;
                        $road = $road->getNextRoad();
                    } while (!is_null($road));
                    $prevRoad->setDirection('left');
                }
            }
        }

    }

    public function getEntryPoints(): array
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

    public function getExitPoints(): array
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
        $exitPoints = $this->getExitPoints();
        foreach ($entryPoints as $entryPoint) {
            foreach ($exitPoints as $exitPoint) {
                if ($entryPoint->getPointProduct() === $exitPoint->getPointProduct()) {
                    $result[] = [
                        'entry' => $entryPoint,
                        'exit' => $exitPoint
                    ];
                }
            }
        }

        return $result;
    }

    public function getRoadCombinationsGroupedByProduct(): array
    {
        $result = [];
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if ($object instanceof EePointRoadObject) {
                    $result[$object->getPointProduct()][] = $object;
                }
            }
        }

        return $result;
    }

    public function iterateMapObjects(): \Generator
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

    /**
     * Подразумеваем, что отрицательных координат у нас нет.
     *
     * @return int
     */
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

    /**
     * Подразумеваем, что отрицательных координат у нас нет.
     *
     * @return int
     */
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

}