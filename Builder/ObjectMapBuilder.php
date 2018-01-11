<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:57
 */

namespace Builder;


use Map\Map;
use Map\Objects\ChestObject;
use Map\Objects\EePointRoadObject;
use Map\Objects\FactoryObject;
use Map\Objects\InserterObject;
use Map\Objects\RoadObject;
use Map\Road;
use Utils\Utils;

class ObjectMapBuilder extends Builder
{

    public function build(): array
    {
        $result = [];
        $schema = $this->tree->countConstructTime($this->count);
        foreach ($schema as $object) {
            if ($object['time'] > 0) {
                $result[] = $this->buildFabric($object);
            } else {
                $result[] = $this->buildSource($object);
            }
        }

        return $result;
    }

    private function buildFabric(array $object): Map
    {
        $fabricMap = new Map();
        $count = (int)ceil($object['time'] / 0.5);

        $isTwoIn = count($object['children']) > 1;
        $topRoadIndex = $fabricMap->addRoad(new Road());
        $bottomRoadIndex = $fabricMap->addRoad(new Road());
        $childrenNames = array_keys($object['children']);
        if ($isTwoIn) $secondBottomRoadIndex = $fabricMap->addRoad(new Road());

        for ($i = 0; $i < $count; $i++) {
            $x = ($i * 3);
            //сама фабрика
            $fabricMap->addObject(
                (new FactoryObject(Utils::getCoords($x,2)))
                    ->setInOut($object['children'], $object['name'])
            );
            //верхний манипулятор на выгрузку
            $fabricMap->addObject(
                (new InserterObject(Utils::getCoords($x + 1,1)))
                    ->setDirection(InserterObject::D_UP)
                    ->setType(InserterObject::T_DEFAULT)
            );
            //нижний стандартный манипулятор
            $fabricMap->addObject(
                (new InserterObject(Utils::getCoords($x,5)))
                    ->setDirection(InserterObject::D_DOWN)
                    ->setType(InserterObject::T_DEFAULT)
            );
            if ($isTwoIn) {
                //нижний длинный манипулятор, если на входе два продукта
                $fabricMap->addObject(
                    (new InserterObject(Utils::getCoords($x + 1,5)))
                        ->setDirection(InserterObject::D_DOWN)
                        ->setType(InserterObject::T_LONG)
                );
            }
        }
        //сверху и снизу строим дорогу справа налево
        for ($j = $count * 3 - 1; $j >= 0 ; $j--) {
            //верхняя
            if ($j === 0) {
                $fabricMap->addRoadObject(
                    (new EePointRoadObject(Utils::getCoords($j, 0)))
                        ->setPointType(EePointRoadObject::T_EXIT)
                        ->setPointProduct($object['name']),
                    $topRoadIndex
                );
            } else {
                $fabricMap->addRoadObject(
                    new RoadObject(Utils::getCoords($j, 0)),
                    $topRoadIndex
                );
            }

            //нижняя
            if ($j === $count * 3 - 1) {
                $fabricMap->addRoadObject(
                    (new EePointRoadObject(Utils::getCoords($j, 6)))
                        ->setPointType(EePointRoadObject::T_ENTRY)
                        ->setPointProduct($object['children'][$childrenNames[0]]),
                    $bottomRoadIndex
                );
            } else {
                $fabricMap->addRoadObject(
                    new RoadObject(Utils::getCoords($j, 6)),
                    $bottomRoadIndex
                );
            }
            //вторая нижняя, если на входе два продукта
            if (isset($secondBottomRoadIndex)) {
                if ($j === $count * 3 - 1) {
                    $fabricMap->addRoadObject(
                        (new EePointRoadObject(Utils::getCoords($j, 7)))
                            ->setPointType(EePointRoadObject::T_ENTRY)
                            ->setPointProduct($object['children'][$childrenNames[1]]),
                        $secondBottomRoadIndex
                    );
                } else {
                    $fabricMap->addRoadObject(
                        new RoadObject(Utils::getCoords($j, 7)),
                        $secondBottomRoadIndex
                    );
                }
            }
        }

        return $fabricMap;

    }

    public function buildSource(array $object): Map
    {
        $sourceMap = new Map();
        $x = 0;
        $y = 0;
        $sourceMap->addObject(
            (new ChestObject(Utils::getCoords($x, $y)))
                ->setStorage($object['name'])
        );
        $sourceMap->addObject(
            (new InserterObject(Utils::getCoords($x, $y + 1)))
                ->setDirection(InserterObject::D_DOWN)
                ->setType(InserterObject::T_DEFAULT)
        );
        $roadIndex = $sourceMap->addRoad(new Road());
        $sourceMap->addRoadObject(
            (new EePointRoadObject(Utils::getCoords($x, $y + 2)))
                ->setPointType(EePointRoadObject::T_EXIT)
                ->setPointProduct($object['name']),
            $roadIndex
        );

        return $sourceMap;
    }

}