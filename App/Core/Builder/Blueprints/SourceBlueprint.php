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

class SourceBlueprint extends BlueprintProto
{

    public function make(array $object): Map
    {
        $sourceMap = new Map();
        $x = 0;
        $y = 0;
        $sourceMap->addObject(
            (new Map\Objects\ChestObject(Utils::c($x, $y)))
                ->setStorage($object['name'])
        );
        $sourceMap->addObject(
            (new Map\Objects\InserterObject(Utils::c($x, $y + 1)))
                ->setDirection(Map\Objects\InserterObject::D_DOWN)
                ->setType(Map\Objects\InserterObject::T_DEFAULT)
        );
        $sourceMap->addObject(
            (new Map\Objects\RoadObject(Utils::c($x, $y + 2)))
                ->setPointType(Map\Objects\RoadObject::T_EXIT)
                ->setLeftSide($object['name'])
        );

        Logger::info('Source added', [
            'out' => $object['name']
        ]);
        return $sourceMap;
    }

}