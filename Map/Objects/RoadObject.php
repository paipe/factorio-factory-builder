<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:36
 */

namespace Map\Objects;


use Map\ObjectProto;
use Map\Road;

class RoadObject extends ObjectProto
{
    const D_UP = 'up';
    const D_RIGHT = 'right';
    const D_DOWN = 'down';
    const D_LEFT = 'left';

    const D_DEFAULT = 'left';

    protected $fileName = 'road';

    protected $width = 1;
    protected $height = 1;

    // направление задается перед отрисовкой
    protected $direction;

    protected $leftSide;
    protected $rightSide;

    protected $prevRoad;
    protected $nextRoad;

    public function getFileName(): string
    {
        return $this->fileName . '_' . $this->direction;
    }

    public function setLeftSide($productName): RoadObject
    {
        $this->leftSide = $productName;
        return $this;
    }

    public function setRightSide($productName): RoadObject
    {
        $this->rightSide = $productName;
        return $this;
    }

    public function getLeftSide(): ?string
    {
        return $this->leftSide;
    }

    public function getRightSide(): ?string
    {
        return $this->rightSide;
    }

    public function getPrevRoad(): ?RoadObject
    {
        return $this->prevRoad;
    }

    public function getNextRoad(): ?RoadObject
    {
        return $this->nextRoad;
    }

    public function setPrevRoad(RoadObject $road, bool $force = false): void
    {
        if (isset($this->prevRoad) && !$force) {
            throw new \Exception('У дороги уже установлен prevRoad');
        }
        $this->prevRoad = $road;
    }

    public function setNextRoad(RoadObject $road, bool $force = false): void
    {
        if (isset($this->nextRoad) && !$force) {
            throw new \Exception('У дороги уже установлен nextRoad');
        }
        $this->nextRoad = $road;
    }

    public function isEmptyLeftSide(): bool
    {
        return !isset($this->leftSide);
    }

    public function isEmptyRightSide(): bool
    {
        return !isset($this->rightSide);
    }

    public function isEmptyPrevRoad(): bool
    {
        return !isset($this->prevRoad);
    }

    public function isEmptyNextRoad(): bool
    {
        return !isset($this->nextRoad);
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }

}
