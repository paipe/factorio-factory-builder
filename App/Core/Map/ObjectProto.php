<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:29
 */

declare(strict_types=1);

namespace App\Core\Map;

/**
 * Абстрактный класс для всех объектов, которые располагаются
 * на сетке карты (объект Map)
 *
 * Class ObjectProto
 * @package App\Core\Map
 */
abstract class ObjectProto
{
    /**
     * Имя файла, используемое при отрисовке карты
     *
     * @var string
     */
    protected $fileName;

    /**
     * Ширина объекта на карте (1 клетка или больше)
     *
     * @var int
     */
    protected $width;

    /**
     * Высота объекта на карте (1 клетка или больше)
     *
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    public function __construct(array $coordinates)
    {
        $this->x = $coordinates['x'];
        $this->y = $coordinates['y'];
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Использовать только при мердже карт!
     *
     * @param int $x
     * @return self
     */
    public function setX(int $x): self
    {
        $this->x = $x;
        return $this;
    }

    /**
     * Использовать только при мердже карт!
     *
     * @param int $y
     * @return self
     */
    public function setY(int $y): self
    {
        $this->y = $y;
        return $this;
    }

    public function getCoordinates(): array
    {
        return ['x' => $this->x, 'y' => $this->y];
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getAdditionalFileName(): ?string
    {
        return null;
    }


}