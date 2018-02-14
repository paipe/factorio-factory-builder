<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:25
 */

declare(strict_types=1);

namespace App\Core\Map\Objects;


use App\Core\Map\ObjectProto;

/**
 * Объект манипулятора, имеет направление перекладывания
 * и тип манипулятора (обычный, длинный, быстрый и т.д.)
 *
 * Class InserterObject
 * @package App\Core\Map\Map\Objects
 */
class InserterObject extends ObjectProto
{
    /** Направление */
    const D_UP = 'up';
    const D_RIGHT = 'right';
    const D_DOWN = 'down';
    const D_LEFT = 'left';

    /** Тип */
    const T_DEFAULT = 'default';
    const T_LONG = 'long_handed';

    /**
     * @var string
     */
    protected $fileName = 'inserter';

    /**
     * @var int
     */
    protected $width = 1;

    /**
     * @var int
     */
    protected $height = 1;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var string
     */
    protected $type = self::T_DEFAULT;

    public function setDirection(string $direction): InserterObject
    {
        $this->direction = $direction;
        return $this;
    }

    public function setType(string $type): InserterObject
    {
        $this->type = $type;
        return $this;
    }

    public function getFileName(): string
    {
        return ($this->type !== self::T_DEFAULT ? $this->type . '_' : '') . $this->fileName . '_' . $this->direction;
    }

}