<?php
/**
 * Created by PhpStorm.
 * User: alexander.panov
 * Date: 9/26/2017
 * Time: 11:40 AM
 */

namespace Planner;


use Builder\BuildObject;

class LinePlanner extends Planner {

    public function plan($buildObjects)
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

    protected function buildRoads()
    {
        $in = [];
        $out = [];
        foreach ($this->roadSchema as $coords => $item) {
            $nameValue = explode('_', $item);
            $direction = array_shift($nameValue);
            $productName = implode('_', $nameValue);
            switch ($direction) {
                case Planner::IN:
                    $in[$coords] = $productName;
                    break;
                case Planner::OUT:
                    $out[$coords] = $productName;
                    break;
            }
        }
        foreach ($out as $outCoords => $outDot) {
            foreach ($in as $inCoords => $inDot) {
                if ($outDot == $inDot) {
                    $road = $this->pathFinder->findPath($this->map, explode(':', $outCoords), explode(':', $inCoords));
                    //разворачиваем, т.к. в исходном варианте массив идет от конца дороги к началу, что не очень удобно
                    $road = array_reverse($road);
                    //рисуем сразу после того, как построили путь, чтобы новый путь шел в обход построенных дорог
                    foreach ($road as $key => $dot) {
                        switch ($key) {
                            case 0:
                                $this->addItemToScheme(
                                    $dot[0], $dot[1],
                                    [
                                        'name' => BuildObject::M_PATH,
                                        'index' => $this->roadIndex,
                                        'start' => ['y' => $dot[0], 'x' => $dot[1]]
                                    ]
                                );
                                break;
//                            case count($road) - 1:
//                                break;
                            default:
                                $this->addItemToScheme(
                                    $dot[0], $dot[1],
                                    [
                                        'name' => BuildObject::M_PATH,
                                        'index' => $this->roadIndex
                                    ]
                                );
                                break;
                        }
                        $this->putObjectOnTheMap($dot[0], $dot[1], BuildObject::M_PATH);
                    }
                    $this->roadIndex++;
                }
            }
        }

    }

}