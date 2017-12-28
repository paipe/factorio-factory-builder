<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:42
 */

namespace Map\Objects;


use Map\ObjectProto;

class ChestObject extends ObjectProto
{
    protected $width = 1;
    protected $height = 1;

    protected $storage;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

}