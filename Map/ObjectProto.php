<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:29
 */

namespace Map;


abstract class ObjectProto
{
    protected $width;
    protected $height;

    protected $x;
    protected $y;

    public function __construct($coordinates)
    {
        $this->x = $coordinates['x'];
        $this->y = $coordinates['y'];
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }


}