<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:59
 */

namespace Map\Objects;


use Map\ObjectProto;

class FactoryObject extends ObjectProto
{
    protected $width = 3;
    protected $height = 3;
    protected $index;

    protected $out;
    protected $in;

    protected static $globalIndex = 0;

    public function __construct(string $out, array $in)
    {
        $this->index = ++self::$globalIndex;
        $this->out = $out;
        $this->in = $in;
    }


}