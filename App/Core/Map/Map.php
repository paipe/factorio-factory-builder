<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:23
 */

declare(strict_types=1);

namespace App\Core\Map;


use App\Core\Map\ObjectProto;
use App\Core\Map\Objects\RoadObject;
use App\Exceptions\PlaceToAddOccupiedException;

/**
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

    public function getEntryPoints(): array
    {
        $entryPoints = [];
        foreach ($this->grid as $row) {
            foreach ($row as $object) {
                if (
                    $object instanceof RoadObject &&
                    $object->getPointType() === RoadObject::T_ROAD_GOAL
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
                    $object->getPointType() === RoadObject::T_ROAD_START
                ) {
                    $exitPoints[] = $object;
                }
            }
        }

        return $exitPoints;
    }

    public function getRoadPointsCombinations(): array
    {
        $preResult = [];
        $entryPoints = $this->getEntryPoints();
        $exitPoints = $this->getExitPoints();
        /** @var RoadObject[] $points */
        $points = array_merge($entryPoints, $exitPoints);
        foreach ($points as $point) {
            $key = implode(':', [$point->getLeftSide(), $point->getRightSide()]);
            if ($key !== ':') {
                $preResult[$key][] = $point;
            }
        }

        $result = [];
        /** @var RoadObject[] $item */
        foreach ($preResult as $item) {
            if (count($item) > 3) {
                $result[] = $item;
            } elseif (count($item) === 2) {
                if ($item[0]->getPointType() === RoadObject::T_ROAD_START) {
                    $result[] = [
                        'start' => $item[0],
                        'goal'  => $item[1]
                    ];
                } else {
                    $result[] = [
                        'goal' => $item[0],
                        'start' => $item[1]
                    ];
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