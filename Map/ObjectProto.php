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
    protected $fileName;

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

    //ЮЗАТЬ ТОЛЬКО ПРИ МЕРДЖЕ КАРТ
    public function setX($x)
    {
        $this->x = $x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public function getCoordinates()
    {
        return ['x' => $this->x, 'y' => $this->y];
    }

    public function getFileName()
    {
        return $this->fileName;
    }


}