<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 10:57
 */

namespace Tree;

class Item extends Component
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $constructTime = 0;

    /**
     * Item constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $number
     * @return array
     */
    public function countItems($number)
    {
        $result = [];
        for ($i = 0; $i < $number; $i++) {
            $result[] = $this->name;
        }
        return $result;
    }

    public function countConstructTime($number, $parent = self::ROOT)
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

    protected function getName()
    {
        return $this->name;
    }
}