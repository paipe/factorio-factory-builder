<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30.01.18
 * Time: 17:03
 */

namespace App\Core\Builder\Blueprints;


use App\Core\Builder\BlueprintProto;
use App\Core\Map\Map;
use App\Core\Map\Objects\ChestObject;
use App\Core\Map\Objects\InserterObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;

class SourceBlueprint extends BlueprintProto
{

    public function make(array $object): Map
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
        $sourceMap->addObject(
            (new RoadObject(Utils::c($x, $y + 2)))
                ->setPointType(RoadObject::T_ROAD_START)
                ->setLeftSide($object['name'])
        );

        Logger::info('Source added', [
            'out' => $object['name']
        ]);
        return $sourceMap;
    }

}