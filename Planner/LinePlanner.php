<?php
/**
 * Created by PhpStorm.
 * User: alexander.panov
 * Date: 9/26/2017
 * Time: 11:40 AM
 */

namespace Planner;


class LinePlanner extends Planner {

    public function plan() {
        $buildObjects = $this->builder->build();
        $this->prepare($buildObjects);
        $this->drawer->draw($this->map, $this->schema);
    }

    private function prepare($buildObjects)
    {
        $x = 0;
        $y = 0;
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

}