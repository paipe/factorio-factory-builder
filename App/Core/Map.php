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
use App\Core\Map\Objects\RoadObject;
use App\Exceptions\PlaceToAddOccupiedException;

/**
 * @todo: класс карты должен быть чисто контейнером
 * всю обработку разнести нафиг
 *
 * Class Map
 * @package App\Core
 */
class Map
{
    private $grid = [];

    public function addObject(ObjectProto $object): ObjectProto
    {
        for ($y = 0; $y < $object->getHeight(); $y++) {
            for ($x = 0; $x < $object->getWidth(); $x++) {
                if (isset($this->grid[$object->getY() + $y][$object->getX() + $x])) {
                    throw new PlaceToAddOccupiedException();
                }
                $this->grid[$object->getY() + $y][$object->getX() + $x] = $object;
            }
        }

        return $object;
    }

    public function removeObject(array $coordinates): void
    {
        unset($this->grid[$coordinates['y']][$coordinates['x']]);
    }

    public function getObjectByCoordinates(array $coordinates): ?ObjectProto
    {
        return $this->grid[$coordinates['y']][$coordinates['x']] ?? null;
    }

    public function isEmptyCoordinates(array $coordinates): bool
    {
        return isset($this->grid[$coordinates['y']][$coordinates['x']]);
    }

    public function processRoadDirections(): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
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

    public function getEntryPoints(): array
    {
        $entryPoints = [];
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if (
                    $object instanceof RoadObject &&
                    $object->getPointType() === RoadObject::T_ENTRY
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
                    $object instanceof RoadObject &&
                    $object->getPointType() === RoadObject::T_EXIT
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
         * @var RoadObject[] $entryPoints
         * @var RoadObject[] $exitPoints
         */
        $entryPoints = $this->getEntryPoints();
        $exitPoints = $this->getExitPoints();
        foreach ($entryPoints as $entryPoint) {
            foreach ($exitPoints as $exitPoint) {
                if (
                    $entryPoint->getLeftSide() === $exitPoint->getLeftSide() &&
                    $entryPoint->getRightSide() === $exitPoint->getRightSide()
                ) {
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
                if ($object instanceof RoadObject && $object->getPointType() !== null) {
                    $result[$object->getPointProduct()][] = $object;
                }
            }
        }

        return $result;
    }

    /**
     * @return \Generator
     */
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