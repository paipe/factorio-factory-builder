<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 11:06
 */

declare(strict_types=1);

namespace Tree;

class Composite extends Component
{
    /**
     * @var Component[]
     */
    private $resources = [];

    public function __construct(string $name, float $constructTime)
    {
        $this->name = $name;
        $this->constructTime = $constructTime;
    }

    public function countItems(int $number): array
    {
        $result = [];
        foreach ($this->resources as $resource) {
            $count = $resource->countItems($number);
            $result = array_merge($result, $count);
        }

        return $result;
    }

    public function countConstructTime(float $number, string $parent = self::ROOT): array
    {
        $result = [
            $this->name => [
                'name'     => $this->name,
                'time'     => $this->constructTime * $number,
                'count'    => $number,
                'parent'   => [$parent],
                'children' => array_count_values($this->getChildrenNames())
            ]
        ];
        foreach ($this->resources as $resource) {
            $itemsToAdd = $resource->countConstructTime($number, $this->name);
            foreach ($itemsToAdd as $name => $item) {
                if (isset($result[$name])) {
                    $result[$name]['time'] += $item['time'];
                    $result[$name]['count'] += $item['count'];
                    $result[$name]['parent'] = array_unique(array_merge($result[$name]['parent'], $item['parent']));
                } else {
                    $result[$name] = $item;
                }
            }
        }

        return $result;
    }

    public function addItems(Component $component, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->resources[] = $component;
        }
    }

    private function getChildrenNames(): array
    {
        $result = [];
        foreach ($this->resources as $resource) {
            $result[] = $resource->getName();
        }
        
        return $result;
    }

}