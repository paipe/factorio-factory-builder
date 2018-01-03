<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 13:43
 */

namespace Map;


use Map\Objects\RoadObject;
use Utils\Stack;

class Road
{
    /**
     * @var Stack
     */
    private $path;

    /**
     * имя продукта, который занимает ту или иную сторону дороги
     */
    private $leftSide = null;
    private $rightSide = null;

    public function __construct()
    {
        $this->path = new Stack();
    }

    public function implodeRoads(Road $road)
    {
        // имплодим К дороге, у которой на верху стека лежит
        // середина будущей дороги
        if ($this->leftSide != $road->getLeftSide()) {
            throw new \Exception('Левые части дороги не совпадают!');
        }
        if ($this->rightSide != $road->getRightSide()) {
            throw new \Exception('Правые части дороги не совпадают!');
        }

        $roadToMerge = [];
        while (!$road->isRoadEmpty()) {
            $roadToMerge[] = $road->removeLastRoad();
        }

        $lastRoadToMerge = array_pop($roadToMerge);
        $topRoad = $this->path->top();
        if (
            $lastRoadToMerge->getX() != $topRoad->getX() ||
            $lastRoadToMerge->getY() != $topRoad->getY()
        ) {
            throw new \Exception('Слияние дорог невозможно: дороги не пересекаются!');
        }

        while (!empty($roadToMerge)) {
            $this->path->push(array_pop($roadToMerge));
        }
    }

    public function explodeRoad(): array
    {

    }

    public function continuePath(RoadObject $object)
    {
        // проверяем неразрывность пути уровнем выше (Map)
        // хотя мне это что-то совсем не нравится
        // (без разрывов, без перехода по диагонали)
        $this->path->push($object);
    }

    public function lastRoad(): ?RoadObject
    {
        return $this->path->top();
    }

    public function removeLastRoad(): ?RoadObject
    {
        return $this->path->pop();
    }

    public function isRoadEmpty(): bool
    {
        return $this->path->isEmpty();
    }

    public function getLeftSide()
    {
        return $this->leftSide;
    }

    public function getRightSide()
    {
        return $this->rightSide;
    }


}