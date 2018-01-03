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
    const D_UNKNOWN = 'unknown';

    const D_DEFAULT = 'left';

    protected $width = 1;
    protected $height = 1;

    // направление задается перед отрисовкой
    protected $direction;

}