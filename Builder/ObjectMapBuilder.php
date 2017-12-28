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
use Map\Objects\FactoryObject;
use Map\Objects\InserterObject;
use Map\Objects\RoadObject;

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
        $x = 0; //начинаем с левого края
        $y = 0;
        for ($i = 0; $i < $count; $i++) {
            $x = $x + ($i * 3);
            //сама фабрика
            $fabricMap->addObject(
                new FactoryObject($object['name'], $object['children']),
                ['x' => $x, 'y' => $y + 2]
            );
            //верхний манипулятор на выгрузку
            $fabricMap->addObject(
                new InserterObject(InserterObject::D_UP, InserterObject::T_DEFAULT),
                ['x' => $x + 1, 'y' => $y + 1]
            );
            //нижний стандартный манипулятор
            $fabricMap->addObject(
                new InserterObject(InserterObject::D_DOWN, InserterObject::T_DEFAULT),
                ['x' => $x, 'y' => $y + 5]
            );
            if (count($object['children']) > 1) {
                //нижний длинный манипулятор, если на входе два продукта
                $fabricMap->addObject(
                    new InserterObject(InserterObject::D_DOWN, InserterObject::T_LONG),
                    ['x' => $x + 1, 'y' => $y + 5]
                );
            }
            //сверху и снизу строим дорогу справа налево
            for ($j = 0; $j < 3; $j++) {
                //верхняя
                $fabricMap->addObject(
                    new RoadObject(RoadObject::D_LEFT),
                    ['x' => $x + $j, 'y' => $y]
                );
                //нижняя
                $fabricMap->addObject(
                    new RoadObject(RoadObject::D_LEFT),
                    ['x' => $x + $j, 'y' => $y + 6]
                );
                //вторая нижняя, если на входе два продукта
                if (count($object['children']) > 1) {
                    $fabricMap->addObject(
                        new RoadObject(RoadObject::D_LEFT),
                        ['x' => $x + $j, 'y' => $y + 7]
                    );
                }
            }
        }

        $fabricMap->addExitPoint($object['name'], ['x' => 0, 'y' => 0]);
        $x = 3 * $count;
        $y = 6;
        foreach ($object['in'] as $product) {
            $fabricMap->addEntryPont($product, ['x' => $x, 'y' => $y]);
            $y++;
        }

        return $fabricMap;

    }

    public function buildSource(array $object): Map
    {
        $sourceMap = new Map();
        $x = 0;
        $y = 0;
        $sourceMap->addObject(
            new ChestObject($object['name']),
            ['x' => $x, 'y' => $y]
        );
        $sourceMap->addObject(
            new InserterObject(InserterObject::D_DOWN, InserterObject::T_DEFAULT),
            ['x' => $x, 'y' => $y + 1]
        );
        $sourceMap->addObject(
            new RoadObject(RoadObject::D_LEFT),
            ['x' => $x, 'y' => $y + 2]
        );
        $sourceMap->addExitPoint($object['name'], ['x' => 0, 'y' => 2]);

        return $sourceMap;
    }

}