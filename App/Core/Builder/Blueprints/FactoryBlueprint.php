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
use App\Core\Utils\PathFinder\PathFinder;
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
     *
     * @param array $object
     * @return Map
     */
    public function make(array $object): Map
    {
        $this->count = (int)ceil($object['time'] / 0.5);
        $this->isTwoIn = count($object['children']) > 1;
        $children = array_keys($object['children']);

        $this
            ->addFabrics($object['name'], $object['children'])
            ->addInserters()
            ->addTopRoad($object['name'])
            ->addBottomRoad($children[0]);

        if ($this->isTwoIn) {
            $this->addSecondBottomRoad($children[1]);
        }


        Logger::info('Fabric added', [
            'out' => $object['name'],
            'in'  => implode(', ', $children)
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

    public function addTopRoad($name)
    {
        $leftSideX = 0;
        $rightSideX = $this->count * 3 - 1;
        $roadManager = new Map\RoadManager();
        $mapManager = new Map\MapManager();

        $goal = (new RoadObject(Utils::c($leftSideX, 0)))
            ->setPointType(RoadObject::T_ROAD_GOAL)
            ->setRightSide($name);
        $start = (new RoadObject(Utils::c($rightSideX, 0)))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setRightSide($name);

        $this->blueprintMap->addObject($goal);
        $this->blueprintMap->addObject($start);

        $road = $roadManager->findPath(
            $this->blueprintMap,
            $start,
            $goal
        );
        $mapManager->mergeRoadToMap($this->blueprintMap, $road, Utils::c(0, 0));


        return $this;
    }

    public function addBottomRoad($name)
    {
        $leftSideX = 0;
        $rightSideX = $this->count * 3 - 1;
        $roadManager = new Map\RoadManager();
        $mapManager = new Map\MapManager();

        $entry = (new RoadObject(Utils::c($rightSideX, 6)))
            ->setPointType(RoadObject::T_ROAD_GOAL);
        if (Utils::isSource($name)) {
            $entry->setLeftSide($name);
        } else {
            $entry->setRightSide($name);
        }
        $this->blueprintMap->addObject($entry);

        $exit = new RoadObject(Utils::c($leftSideX, 6));
        if (Utils::isSource($name)) {
            $exit->setLeftSide($name);
        } else {
            $exit->setRightSide($name);
        }
        $this->blueprintMap->addObject($exit);

        $road = $roadManager->findPath(
            $this->blueprintMap,
            $entry,
            $exit
        );
        $mapManager->mergeRoadToMap($this->blueprintMap, $road, Utils::c(0, 0));

        return $this;
    }

    public function addSecondBottomRoad($name)
    {
        $leftSideX = 0;
        $rightSideX = $this->count * 3 - 1;
        $roadManager = new Map\RoadManager();
        $mapManager = new Map\MapManager();

        $entry = (new RoadObject(Utils::c($rightSideX, 7)))
            ->setPointType(RoadObject::T_ROAD_GOAL);
        if (Utils::isSource($name)) {
            $entry->setLeftSide($name);
        } else {
            $entry->setRightSide($name);
        }
        $this->blueprintMap->addObject($entry);

        $exit = new RoadObject(Utils::c($leftSideX, 7));
        if (Utils::isSource($name)) {
            $exit->setLeftSide($name);
        } else {
            $exit->setRightSide($name);
        }
        $this->blueprintMap->addObject($exit);

        $road = $roadManager->findPath(
            $this->blueprintMap,
            $entry,
            $exit
        );
        $mapManager->mergeRoadToMap($this->blueprintMap, $road, Utils::c(0, 0));

        return $this;
    }


}