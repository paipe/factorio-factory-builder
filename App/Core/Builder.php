<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:57
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\Objects\ChestObject;
use App\Core\Map\Objects\EePointRoadObject;
use App\Core\Map\Objects\FactoryObject;
use App\Core\Map\Objects\InserterObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Parser\Component;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;

/**
 * Строит мини-схемы на основании дерева компонентов
 *
 * Class Builder
 * @package App\Core
 */
class Builder
{
    /**
     * @var Component
     */
    protected $tree;

    /**
     * @var int per second
     */
    protected $count;

    public function setTree(Component $tree): Builder
    {
        $this->tree = $tree;
        return $this;
    }

    public function setCount(float $count): Builder
    {
        $this->count = $count;
        return $this;
    }

    public function build(): array
    {
        $result = [];
        $schema = $this->tree->countConstructTime($this->count);
        Logger::info('Start building objects', $schema);
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
        for ($i = 0; $i < $count; $i++) {
            $x = ($i * 3);
            //сама фабрика
            $fabricMap->addObject(
                (new FactoryObject(Utils::c($x,2)))
                    ->setInOut($object['children'], $object['name'])
            );
            //верхний манипулятор на выгрузку
            $fabricMap->addObject(
                (new InserterObject(Utils::c($x + 1,1)))
                    ->setDirection(InserterObject::D_UP)
                    ->setType(InserterObject::T_DEFAULT)
            );
            //нижний стандартный манипулятор
            $fabricMap->addObject(
                (new InserterObject(Utils::c($x,5)))
                    ->setDirection(InserterObject::D_UP)
                    ->setType(InserterObject::T_DEFAULT)
            );
            if ($isTwoIn) {
                //нижний длинный манипулятор, если на входе два продукта
                $fabricMap->addObject(
                    (new InserterObject(Utils::c($x + 1,5)))
                        ->setDirection(InserterObject::D_UP)
                        ->setType(InserterObject::T_LONG)
                );
            }
        }
        //сверху и снизу строим дорогу справа налево
        for ($j = $count * 3 - 1; $j >= 0 ; $j--) {
            //верхняя
            if ($j === 0) {
                $fabricMap->addRoadObject(
                    (new EePointRoadObject(Utils::c($j, 0)))
                        ->setPointType(EePointRoadObject::T_EXIT)
                        ->setPointProduct($object['name'])
                        ->setRightSide($object['name'])
                );
            } else {
                $fabricMap->addRoadObject(
                    (new RoadObject(Utils::c($j, 0)))
                        ->setRightSide($object['name'])
                );
            }

            //нижняя
            if ($j === $count * 3 - 1) {
                $road = (new EePointRoadObject(Utils::c($j, 6)))
                    ->setPointType(EePointRoadObject::T_ENTRY)
                    ->setPointProduct(array_keys($object['children'])[0]);
                if (Utils::isSource(array_keys($object['children'])[0])) {
                    $road->setLeftSide(array_keys($object['children'])[0]);
                } else {
                    $road->setRightSide(array_keys($object['children'])[0]);
                }
                $fabricMap->addRoadObject($road);
            } else {
                $road = new RoadObject(Utils::c($j, 6));
                if (Utils::isSource(array_keys($object['children'])[0])) {
                    $road->setLeftSide(array_keys($object['children'])[0]);
                } else {
                    $road->setRightSide(array_keys($object['children'])[0]);
                }
                $fabricMap->addRoadObject($road);
            }
            //вторая нижняя, если на входе два продукта
            if ($isTwoIn) {
                if ($j === $count * 3 - 1) {
                    $road = (new EePointRoadObject(Utils::c($j, 7)))
                        ->setPointType(EePointRoadObject::T_ENTRY)
                        ->setPointProduct(array_keys($object['children'])[1]);
                    if (Utils::isSource(array_keys($object['children'])[1])) {
                        $road->setLeftSide(array_keys($object['children'])[1]);
                    } else {
                        $road->setRightSide(array_keys($object['children'])[1]);
                    }
                    $fabricMap->addRoadObject($road);
                } else {
                    $road = new RoadObject(Utils::c($j, 7));
                    if (Utils::isSource(array_keys($object['children'])[1])) {
                        $road->setLeftSide(array_keys($object['children'])[1]);
                    } else {
                        $road->setRightSide(array_keys($object['children'])[1]);
                    }
                    $fabricMap->addRoadObject($road);
                }
            }
        }

        Logger::info('Fabric added', [
            'out' => $object['name'],
            'in'  => implode(', ', array_keys($object['children']))
        ]);

        return $fabricMap;

    }

    public function buildSource(array $object): Map
    {
        $sourceMap = new Map();
        $x = 0;
        $y = 0;
        $sourceMap->addObject(
            (new ChestObject(Utils::c($x, $y)))
                ->setStorage($object['name'])
        );
        $sourceMap->addObject(
            (new InserterObject(Utils::c($x, $y + 1)))
                ->setDirection(InserterObject::D_DOWN)
                ->setType(InserterObject::T_DEFAULT)
        );
        $sourceMap->addRoadObject(
            (new EePointRoadObject(Utils::c($x, $y + 2)))
                ->setPointType(EePointRoadObject::T_EXIT)
                ->setPointProduct($object['name'])
                ->setLeftSide($object['name'])
        );

        Logger::info('Source added', [
            'out' => $object['name']
        ]);
        return $sourceMap;
    }

}