<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:22
 */

declare(strict_types=1);

namespace App\Core;

use Exceptions\UnsupportedMethodException;

/**
 * Абстрактный класс для построения дерева продуктов
 *
 * Class Component
 * @package App\Core
 */
abstract class Component
{
    protected const ROOT = 'root';

    protected $name;
    protected $constructTime;

    abstract public function countItems(int $number): array;
    
    abstract public function countConstructTime(float $number, string $parent = self::ROOT): array;

    public function addItems(Component $component, int $count): void
    {
        throw new UnsupportedMethodException();
    }

    protected function getName(): string
    {
        return $this->name;
    }
}