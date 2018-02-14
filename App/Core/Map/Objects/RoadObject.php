<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:36
 */

declare(strict_types=1);

namespace App\Core\Map\Objects;


use App\Core\Map\Conductor;
use App\Core\Map\ObjectProto;

/**
 * Объект дороги
 * Хранит информацию о ресурсе на левой и правой стороне,
 * а так же ссылки на предыдущую и следующую дорогу в цепочке
 *
 * Class RoadObject
 * @package App\Core\Map\Map\Objects
 */
class RoadObject extends ObjectProto implements Conductor
{
    /** Направление дороги */
    const D_UP = 'up';
    const D_RIGHT = 'right';
    const D_DOWN = 'down';
    const D_LEFT = 'left';

    const D_DEFAULT = 'left';

    /** Тип точки */
    const T_ROAD_GOAL = 'entry';
    const T_ROAD_START  = 'exit';

    /**
     * @var string
     */
    protected $fileName = 'road';

    /**
     * @var int
     */
    protected $width = 1;

    /**
     * @var int
     */
    protected $height = 1;

    /**
     * Направление задается перед отрисовкой,
     * см. Map::processRoadDirections
     *
     * @var string
     */
    protected $direction;

    /**
     * @var string
     */
    protected $pointType;

    /**
     * @var string
     */
    protected $leftSide;

    /**
     * @var string
     */
    protected $rightSide;

    /**
     * @var RoadObject
     */
    protected $prevRoad;

    /**
     * @var RoadObject
     */
    protected $nextRoad;

    public function getFileName(): string
    {
        return $this->fileName . '_' . $this->direction;
    }

    public function setLeftSide(?string $productName): self
    {
        if ($productName !== null) {
            $this->leftSide = $productName;
        }
        return $this;
    }

    public function setRightSide(?string $productName): self
    {
        if ($productName !== null) {
            $this->rightSide = $productName;
        }
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

    public function getPrevObject(): ?self
    {
        return $this->prevRoad;
    }

    public function getNextObject(): ?self
    {
        return $this->nextRoad;
    }

    /**
     * @param Conductor $road
     * @return self
     * @throws \Exception
     */
    public function setPrevObject(Conductor $road): self
    {
        if (isset($this->prevRoad)) {
            throw new \Exception('У дороги уже установлен prevRoad');
        }
        $this->prevRoad = $road;

        return $this;
    }

    /**
     * @param Conductor $road
     * @return self
     * @throws \Exception
     */
    public function setNextObject(Conductor $road): self
    {
        if (isset($this->nextRoad)) {
            throw new \Exception('У дороги уже установлен nextRoad');
        }
        $this->nextRoad = $road;

        return $this;
    }

    public function clearPrevObject()
    {
        $this->prevRoad = null;
    }

    public function clearNextObject()
    {
        $this->nextRoad = null;
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

    public function setPointType(string $pointType): self
    {
        $this->pointType = $pointType;
        return $this;
    }

    public function getPointType(): ?string
    {
        return $this->pointType;
    }

    public function clearPointType()
    {
        $this->pointType = null;
    }

}
