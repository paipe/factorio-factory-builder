<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.02.18
 * Time: 17:27
 */

namespace App\Core\Map\Objects;


use App\Core\Map\ObjectProto;

class SplitterObject extends ObjectProto
{

    protected $fileName = 'splitter';
    protected $width = 1;
    protected $height = 2;

    public function getFirstExitCoordinates()
    {
        return ['x' => $this->getX() - 1, 'y' => $this->getY()];
    }

    public function getSecondExitCoordinates()
    {
        return ['x' => $this->getX() - 1, 'y' => $this->getY() + 1];
    }

    public function getEntryCoordinates()
    {
        return ['x' => $this->getX() + 1, 'y' => $this->getY()];
    }


}