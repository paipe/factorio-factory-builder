<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30.01.18
 * Time: 17:03
 */

namespace App\Core\Builder\Blueprints;


use App\Core\Builder\BlueprintProto;
use App\Core\Map;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;
use App\Core\Map\Objects\InserterObject;
use App\Core\Map\Objects\RoadObject;


class FactoryBlueprint extends BlueprintProto
{
    private $count;
    private $isTwoIn;

    /**
     * Собирает план фабрики
     * @todo: возможно, прямые дороги должен строить PathFinder
     * @todo: но тогда его нужно зарефакторить
     *
     * @param array $object
     * @return Map
     */
    public function make(array $object): Map
    {
        $this->count = (int)ceil($object['time'] / 0.5);
        $this->isTwoIn = count($object['children']) > 1;

        $this
            ->addFabrics($object['name'], $object['children'])
            ->addInserters()
            ->addRoads($object['name'], array_keys($object['children']));

        Logger::info('Fabric added', [
            'out' => $object['name'],
            'in'  => implode(', ', array_keys($object['children']))
        ]);

        return $this->blueprintMap;
    }

    /**
     * Добавляет фабрики на план
     *
     * @param $name
     * @param $children
     *
     * @return self
     */
    private function addFabrics($name, $children): self
    {
        for ($i = 0; $i < $this->count; $i++) {
            $x = ($i * 3);
            $this->blueprintMap->addObject(
                (new Map\Objects\FactoryObject(Utils::c($x, 2)))
                    ->setInOut($children, $name)
            );
        }

        return $this;
    }

    /**
     *  Добавляет манипуляторы на план
     *
     * @return self
     */
    private function addInserters(): self
    {
        for ($i = 0; $i < $this->count; $i++) {
            $x = ($i * 3);
            //верхний манипулятор на выгрузку
            $this->blueprintMap->addObject(
                (new InserterObject(Utils::c($x + 1,1)))
                    ->setDirection(InserterObject::D_UP)
                    ->setType(InserterObject::T_DEFAULT)
            );
            //нижний стандартный манипулятор
            $this->blueprintMap->addObject(
                (new InserterObject(Utils::c($x,5)))
                    ->setDirection(InserterObject::D_UP)
                    ->setType(InserterObject::T_DEFAULT)
            );
            if ($this->isTwoIn) {
                //нижний длинный манипулятор, если на входе два продукта
                $this->blueprintMap->addObject(
                    (new InserterObject(Utils::c($x + 1,5)))
                        ->setDirection(InserterObject::D_UP)
                        ->setType(InserterObject::T_LONG)
                );
            }
        }

        return $this;
    }

    /**
     * Добавляет дороги
     * @todo переделать под PathFinder или иным способом избавиться от Map::addRoadObject
     *
     * @param $name
     * @param $children
     * @return FactoryBlueprint
     */
    private function addRoads($name, $children): self
    {
        //сверху и снизу строим дорогу справа налево
        for ($j = $this->count * 3 - 1; $j >= 0 ; $j--) {
            //верхняя
            if ($j === 0) {
                $this->blueprintMap->addRoadObject(
                    (new RoadObject(Utils::c($j, 0)))
                        ->setPointType(RoadObject::T_EXIT)
                        ->setRightSide($name)
                );
            } else {
                $this->blueprintMap->addRoadObject(
                    (new RoadObject(Utils::c($j, 0)))
                        ->setRightSide($name)
                );
            }

            //нижняя
            if ($j === $this->count * 3 - 1) {
                $road = (new RoadObject(Utils::c($j, 6)))
                    ->setPointType(RoadObject::T_ENTRY);
                if (Utils::isSource($children[0])) {
                    $road->setLeftSide($children[0]);
                } else {
                    $road->setRightSide($children[0]);
                }
                $this->blueprintMap->addRoadObject($road);
            } else {
                $road = new RoadObject(Utils::c($j, 6));
                if (Utils::isSource($children[0])) {
                    $road->setLeftSide($children[0]);
                } else {
                    $road->setRightSide($children[0]);
                }
                $this->blueprintMap->addRoadObject($road);
            }
            //вторая нижняя, если на входе два продукта
            if ($this->isTwoIn) {
                if ($j === $this->count * 3 - 1) {
                    $road = (new RoadObject(Utils::c($j, 7)))
                        ->setPointType(RoadObject::T_ENTRY);
                    if (Utils::isSource($children[1])) {
                        $road->setLeftSide($children[1]);
                    } else {
                        $road->setRightSide($children[1]);
                    }
                    $this->blueprintMap->addRoadObject($road);
                } else {
                    $road = new RoadObject(Utils::c($j, 7));
                    if (Utils::isSource($children[1])) {
                        $road->setLeftSide($children[1]);
                    } else {
                        $road->setRightSide($children[1]);
                    }
                    $this->blueprintMap->addRoadObject($road);
                }
            }
        }

        return $this;
    }


}