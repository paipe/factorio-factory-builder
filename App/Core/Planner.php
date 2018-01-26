<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:53
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\Objects\EePointRoadObject;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;

class Planner
{

    /** Расстоение между мини-схемами */
    const DISTANCE = 4;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Drawer
     */
    protected $drawer;

    /**
     * @var PathFinder
     */
    protected $pathFinder;

    /**
     * @var Map
     */
    protected $objectMap;

    public function __construct(PathFinder $pathFinder)
    {
        $this->pathFinder = $pathFinder;
    }

    /**
     * @param Map[] $buildObjects
     * @return Map
     */
    public function plan(array $buildObjects): Map
    {
        $this->objectMap = new Map();
        $x = 0;
        $y = 0;
        foreach ($buildObjects as $object) {
            $this->objectMap->mergeMaps(
                $object,
                Utils::c($x, $y)
            );
            Logger::info('Object merged to map.', [
                'height' => $object->getHeight(),
                'width'  => $object->getWidth(),
                'x' => $x,
                'y' => $y
            ]);
            //строим пока что все в линию слева на право, так что просто инкрементим X
            $x += $object->getWidth() + self::DISTANCE;
        }

//        $this->buildRoads();
        $this->newBuildRoads();

        return $this->objectMap;
    }

    protected function buildRoads(): void
    {
        $combinations = $this->objectMap->getStartEndRoadCombinations();
        foreach ($combinations as $combination) {
            /**
             * @var EePointRoadObject $entryPoint
             * @var EePointRoadObject $exitPoint
             */
            $entryPoint = $combination['entry'];
            $exitPoint  = $combination['exit'];
            //ищем от конца до начала, чтобы потом не разворачивать массив
            $road = $this->pathFinder->findPath(
                $this->objectMap,
                $entryPoint,
                $exitPoint
            );
            try {
                $this->objectMap->mergeMaps(
                    $road,
                    Utils::c(0, 0)
                );
            } catch (\Error $e) {
                echo 'Кривой мердж карт' . PHP_EOL;
            }
        }
    }

    protected function newBuildRoads(): void
    {
        $pointGroups = $this->objectMap->getRoadCombinationsGroupedByProduct();
        foreach ($pointGroups as $productName => $group) {
            if (count($group) === 1) {
                continue;
            } elseif (count($group) === 2) {
                $this->simpleBuildRoad($group);
            } else {
                $this->magicBuildRoad($group);
            }
        }

    }

    /**
     * @param EePointRoadObject[] $group
     * @throws \Exception
     */
    protected function simpleBuildRoad($group): void
    {
        foreach ($group as $object) {
            if ($object->getPointType() === EePointRoadObject::T_EXIT) {
                /** @var EePointRoadObject $exitPoint */
                $exitPoint = $object;
            }
            if ($object->getPointType() === EePointRoadObject::T_ENTRY) {
                /** @var EePointRoadObject $entryPoint */
                $entryPoint = $object;
            }
        }
        if (!(isset($entryPoint) && isset($exitPoint))) {
            throw new \Exception();
        }
        $road = $this->pathFinder->findPath(
            $this->objectMap,
            $entryPoint,
            $exitPoint
        );
        try {
            $this->objectMap->mergeMaps(
                $road,
                Utils::c(0, 0)
            );
        } catch (\Error $e) {
            echo 'Кривой мердж карт' . PHP_EOL;
        }

        Logger::info('Simple road was built.', [
            'start' => 'x: ' . $entryPoint->getX() . ', y: ' . $entryPoint->getY(),
            'end'   => 'x: ' . $exitPoint->getX() . ', y: ' . $exitPoint->getY(),
            'product' => $entryPoint->getPointProduct()
        ]);
    }

    /**
     * @param EePointRoadObject[] $group
     */
    private function magicBuildRoad(array $group): void
    {
        //todo ветвление и все такое будут тут
    }

}
