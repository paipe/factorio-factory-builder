<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:23
 */

namespace Map;


use Map\Objects\RoadObject;

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

    public function getObjectByCoordinates($coordinates): ?ObjectProto
    {
        return $grid[$coordinates['y']][$coordinates['x']] ?? null;
    }

    public function mergeMaps(Map $map, $parentCoordinates, $childCoordinates)
    {


    }

    public function addEntryPoint($productName, $coordinates)
    {
        $this->entryPoints[$coordinates['y']][$coordinates['x']] = $productName;
    }

    public function addExitPoint($productName, $coordinates)
    {
        $this->exitPoints[$coordinates['y']][$coordinates['x']] = $productName;
    }

    public function getStartEndRoadCombinations(): array
    {

    }

    public function isSpaceAvailable($coordinates): bool
    {

    }

    public function getWidth(): int
    {
        return $this->getMapSize()['width'];
    }

    public function getHeight(): int
    {
        return $this->getMapSize()['height'];
    }


    private function getMapSize(): array
    {
        $minX = PHP_INT_MAX;
        $maxX = -PHP_INT_MAX;
        $minY = PHP_INT_MAX;
        $maxY = -PHP_INT_MAX;
        foreach ($this->grid as $rowKey => $rows) {
            if ($rowKey > $maxY) $maxY = $rowKey;
            if ($rowKey < $minY) $minY = $rowKey;
            foreach ($rows as $pointKey => $point) {
                if ($pointKey > $maxX) $maxX = $pointKey;
                if ($pointKey < $minX) $minX = $pointKey;
            }
        }

        return [
            'width'  => abs($minX) + abs($maxX),
            'height' => abs($minY) + abs($maxY)
        ];
    }
}