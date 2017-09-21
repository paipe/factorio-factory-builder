<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 14:23
 */

namespace Planner;


use Builder\BuildObject;

class SimplePlanner extends Planner
{

    const DISTANCE = 10;

    private $map = [];
    private $schema = [];
    
    public function plan()
    {
        $buildObjects = $this->builder->build();
        $this->prepare($buildObjects);
        $this->drawer->draw($this->map, $this->schema);
    }

    /**
     * @param BuildObject[] $buildObjects
     */
    private function prepare($buildObjects)
    {
        $y = 0;
        $x = 0;
        list($buildObjects, $rowSplitter) = $this->sortObjects($buildObjects);
        foreach ($buildObjects as $buildObject) {
            if ($buildObject->factoryCount > 0) {
                $this->putObjectOnTheMap($y, $x, $buildObject->buildFabric());
                for ($i = 0; $i < $buildObject->factoryCount; $i++) {
                    $this->addItemToScheme($y + 3, $x + ($i * 3) + 1, $buildObject->productName);
                }
            } else {
                $this->putObjectOnTheMap($y, $x, $buildObject->buildSource());
                $this->addItemToScheme($y, $x, $buildObject->productName);
            }

            $x += $buildObject->factoryCount * 3 + self::DISTANCE;
        }
    }

    private function putObjectOnTheMap($objectY, $objectX, $object)
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

    private function addItemToScheme($y, $x, $name)
    {
        $this->schema[$y . ':' . $x] = $name;
    }

    /**
     * @param BuildObject[] $buildObjects
     */
    private function sortObjects($buildObjects)
    {
        $objects = [];
        foreach ($buildObjects as $buildObject) {
            $objects[] = $buildObject->factoryCount > 0 ?
                ['width' => 3 * $buildObject->factoryCount, 'height' => 8] :
                ['width' => 1, 'height' => 8];
        }
    }

}