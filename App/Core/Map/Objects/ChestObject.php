<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 17:42
 */

declare(strict_types=1);

namespace App\Core\Map\Objects;


use App\Core\Map\ObjectProto;

/**
 * Объект сундука, хранит в себе ресурс одного типа
 *
 * Class ChestObject
 * @package App\Core\Map\Objects
 */
class ChestObject extends ObjectProto
{
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
    protected $storage;

    /**
     * @var string
     */
    protected $fileName = 'chest';

    public function setStorage($storage = null): ChestObject
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Используем картинку ресурса вместо сундука
     * если сундук не пустой
     *
     * @return string
     */
    public function getFileName() :string
    {
        return $this->storage ?? $this->fileName;
    }

}