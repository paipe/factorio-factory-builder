<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 13:25
 */

namespace Planner;


use Builder\Builder;
use Builder\BuildObject;
use Drawer\Drawer;

abstract class Planner
{
    const DISTANCE = 10;

    const OUT = 'out_';
    const IN  = 'in_';

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Drawer
     */
    protected $drawer;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $schema = [];

    /**
     * @var array
     */
    protected $roadSchema;

    /**
     * Planner constructor.
     *
     * @param \Builder\Builder $builder
     * @param \Drawer\Drawer $drawer
     */
    public function __construct(Builder $builder, Drawer $drawer)
    {
        $this->builder = $builder;
        $this->drawer  = $drawer;
    }

    /**
     * @return mixed
     */
    abstract public function plan();

    /**
     * @param int $objectY
     * @param int $objectX
     * @param $object
     */
    protected function putObjectOnTheMap($objectY, $objectX, $object)
    {
        if (!is_array($object)) {
            $object = [[$object]];
        }

        $ySize = count($object);
        $xSize = count($object[0]);
        $maxY = max($objectY + $ySize - 1, count($this->map) - 1);
        $maxX = max($objectX + $xSize - 1, isset($this->map[0]) ? count($this->map[0]) - 1 : 0);

        if (!isset($this->map[$maxY]) || !isset($this->map[0][$maxX])) {
            for ($y = 0; $y <= $maxY; $y++) {
                for ($x = 0; $x <= $maxX; $x++) {
                    if (!isset($this->map[$y][$x])) {
                        $this->map[$y][$x] = BuildObject::M_SPACE;
                    }
                }
            }
        }

        for ($y = $objectY; $y < $objectY + $ySize; $y++) {
            for ($x = $objectX; $x < $objectX + $xSize; $x++) {
                $this->map[$y][$x] = $object[$y - $objectY][$x - $objectX];
            }
        }

    }

    /**
     * @param int $y
     * @param int $x
     * @param string $name
     */
    protected function addItemToScheme($y, $x, $name)
    {
        $this->schema[$y . ':' . $x] = $name;
    }

    /**
     * @param int $y
     * @param int $x
     * @param string $name
     */
    protected function addItemToRoadScheme($y, $x, $name)
    {
        $this->roadSchema[$y . ':' . $x] = $name;
    }



}