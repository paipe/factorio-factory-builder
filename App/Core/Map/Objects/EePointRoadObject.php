<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.01.18
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\Core\Map\Objects;

/**
 * Объект дороги, являющийся ключевым для постройки дорог
 * Хранит информацию о входе и выходе
 *
 * @todo: может просто добавить проперти в саму дорогу, нафиг этот огород?
 *
 * Class EePointRoadObject
 * @package Map\Objects
 */
class EePointRoadObject extends RoadObject
{
    /** Тип точки */
    const T_ENTRY = 'entry';
    const T_EXIT  = 'exit';

    /**
     * @var string
     */
    protected $pointType;

    /**
     * @var string
     */
    protected $pointProduct;

    public function setPointType(string $pointType): EePointRoadObject
    {
        $this->pointType = $pointType;
        return $this;
    }

    public function setPointProduct(string $pointProduct): EePointRoadObject
    {
        $this->pointProduct = $pointProduct;
        return $this;
    }

    public function getPointType(): string
    {
        return $this->pointType;
    }

    public function getPointProduct(): string
    {
        return $this->pointProduct;
    }

}