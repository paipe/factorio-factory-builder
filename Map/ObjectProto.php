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

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }


}