<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.01.18
 * Time: 17:23
 */

namespace App\Core\Map\Objects;

/**
 * Entry/Exit point road object
 *
 * Class EePointRoadObject
 * @package Map\Objects
 */
class EePointRoadObject extends RoadObject
{
    const T_ENTRY = 'entry';
    const T_EXIT  = 'exit';

    protected $pointType;

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