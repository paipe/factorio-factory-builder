<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:36
 */

namespace Map\Objects;


use Map\ObjectProto;

class RoadObject extends ObjectProto
{
    const D_UP = 'up';
    const D_RIGHT = 'right';
    const D_DOWN = 'down';
    const D_LEFT = 'left';

    const D_DEFAULT = 'left';

    public static $globalIndex = 0;

    protected $fileName = 'road';
    protected $index;

    protected $width = 1;
    protected $height = 1;

    // направление задается перед отрисовкой
    protected $direction;

    protected $prevRoad;
    protected $nextRoad;

    public function __construct($coordinates, $roadIndex)
    {
        parent::__construct($coordinates);
        $this->index = $roadIndex;
    }


    public function getFileName()
    {
        return $this->fileName . '_' . $this->direction;
    }

    public function getRoadIndex()
    {
        return $this->index;
    }

}
