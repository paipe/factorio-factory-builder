<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:59
 */

declare(strict_types=1);

namespace App\Core\Map\Objects;


use App\Core\Map\ObjectProto;

/**
 * Объект фабрики
 * Хранит уникальный глобальный индекс фабрики (пока не понятно зачем),
 * а так же именя входящих и исходящего продуктов.
 *
 * Class FactoryObject
 * @package App\Core\Map\Map\Objects
 */
class FactoryObject extends ObjectProto
{
    protected $fileName = 'fabric';

    protected $width = 3;
    protected $height = 3;
    protected $index;

    protected $out;
    protected $in;

    protected static $globalIndex = 0;

    public function __construct(array $coordinates)
    {
        parent::__construct($coordinates);
        $this->index = ++self::$globalIndex;
    }

    public function setInOut(array $in, string $out): FactoryObject
    {
        $this->out = $out;
        $this->in = $in;
        return $this;
    }

    public function getAdditionalFileName(): ?string
    {
        return $this->out;
    }


}