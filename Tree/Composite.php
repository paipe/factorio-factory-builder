<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 11:06
 */

namespace Tree;

class Composite extends Component
{
    /**
     * @var Component[]
     */
    private $resources = [];

    /**
     * @var integer
     */
    private $constructTime;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $outputCount;

    /**
     * Composite constructor.
     * @param string $name
     * @param integer $constructTime
     * @param integer $outputCount
     */
    public function __construct($name, $constructTime, $outputCount = 1)
    {
        $this->name = $name;
        $this->constructTime = $constructTime;
        $this->outputCount = $outputCount;
    }

    /**
     * @param int $number
     * @return array
     */
    public function countItems($number)
    {
        $result = [];
        foreach ($this->resources as $resource) {
            $count = $resource->countItems($number);
            $result = array_merge($result, $count);
        }

        return $result;
    }

    /**
     * @param int $number
     * @param string $parent
     * @return array
     */
    public function countConstructTime($number, $parent = self::ROOT)
    {
        $result = [
            $this->name => [
                'name'     => $this->name,
                'output'   => $this->outputCount,
                'time'     => $this->constructTime * $number / $this->outputCount,
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

    /**
     * @param Component $component
     * @param integer $count
     */
    public function addItems($component, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->resources[] = $component;
        }
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }
    
    private function getChildrenNames()
    {
        $result = [];
        foreach ($this->resources as $resource) {
            $result[] = $resource->getName();
        }
        
        return $result;
    }

}