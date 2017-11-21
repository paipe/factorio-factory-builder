<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:22
 */

declare(strict_types=1);

namespace Tree;

use Exceptions\UnsupportedMethodException;

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