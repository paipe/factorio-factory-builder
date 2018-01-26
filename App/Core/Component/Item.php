<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 10:57
 */

declare(strict_types=1);

namespace App\Core\Component;

use App\Core\Component;

class Item extends Component
{

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->constructTime = 0;
    }

    public function countItems(int $number): array
    {
        $result = [];
        for ($i = 0; $i < $number; $i++) {
            $result[] = $this->name;
        }
        return $result;
    }

    public function countConstructTime(float $number, string $parent = self::ROOT): array
    {
        return [
            $this->name => [
                'name'   => $this->name,
                'time'   => $this->constructTime,
                'count'  => 1,
                'parent' => [$parent]
            ]
        ];
    }
}