<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:42
 */

namespace App\Core\Map\Objects;


use App\Core\Map\ObjectProto;

class ChestObject extends ObjectProto
{
    protected $width = 1;
    protected $height = 1;

    protected $storage;
    protected $fileName = 'chest';

    public function setStorage($storage = null)
    {
        $this->storage = $storage;
        return $this;
    }

    public function getFileName()
    {
        return $this->storage ??$this->fileName;
    }

}