<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:25
 */

namespace Map\Objects;


use Map\ObjectProto;

class InserterObject extends ObjectProto
{
    const D_UP = 'up';
    const D_RIGHT = 'right';
    const D_DOWN = 'down';
    const D_LEFT = 'left';

    const T_DEFAULT = 'default';
    const T_LONG = 'long';

    protected $width = 1;
    protected $height = 1;

    protected $direction;
    protected $type;

    public function __construct($direction, $type = self::T_DEFAULT)
    {
        $this->direction = $direction;
        $this->type = $type;
    }

}