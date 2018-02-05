<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.18
 * Time: 17:59
 */

namespace App\Tests\Map;

use App\Core\Map;
use App\Core\Map\MapManager;
use App\Core\Utils\Utils;
use App\Exceptions\PlaceToAddOccupiedException;
use PHPUnit\Framework\TestCase;

class MapManagerTest extends TestCase
{

    public function testMergeMaps()
    {
        $map1 = new Map();
        $map2 = new Map();
        $source1 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);
        $source2 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);
        $map1->addObject($source1);
        $map2->addObject($source2);

        $mapManager = new MapManager();
        $resultMap = $mapManager->mergeMaps($map1, $map2, ['x' => 0, 'y' => 1]);

        $this->assertEquals($source1, $resultMap->getObjectByCoordinates(['x' => 0, 'y' => 0]));
        $this->assertEquals($source2, $resultMap->getObjectByCoordinates(['x' => 0, 'y' => 1]));
    }

    public function testMergeMapWithIntersection()
    {
        $this->expectException(PlaceToAddOccupiedException::class);

        $map1 = new Map();
        $map2 = new Map();
        $source1 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);
        $source2 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);
        $map1->addObject($source1);
        $map2->addObject($source2);

        $mapManager = new MapManager();
        $mapManager->mergeMaps($map1, $map2, ['x' => 0, 'y' => 0]);
    }

    public function testMergeRoadToMap()
    {
        $mapManager = new MapManager();
        $map = new Map();
        $roadMap = new Map();

        $roadStart = (new Map\Objects\RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_EXIT)
            ->setLeftSide('red_bottle');
        $roadGoal = (new Map\Objects\RoadObject(['x' => 4, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ENTRY)
            ->setLeftSide('red_bottle');

        $road2 = (new Map\Objects\RoadObject(['x' => 1, 'y' => 0]))
            ->setLeftSide('red_bottle');
        $road3 = (new Map\Objects\RoadObject(['x' => 2, 'y' => 0]))
            ->setLeftSide('red_bottle');
        $road4 = (new Map\Objects\RoadObject(['x' => 3, 'y' => 0]))
            ->setLeftSide('red_bottle');

        $road2->setNextObject($road3);
        $road3->setNextObject($road4);

        $road4->setPrevObject($road3);
        $road3->setPrevObject($road2);

        $map->addObject($roadStart);
        $map->addObject($roadGoal);
        $roadMap->addObject($road2);
        $roadMap->addObject($road3);
        $roadMap->addObject($road4);

        $mapManager->mergeRoadToMap($map, $roadMap, ['x' => 0, 'y' => 0]);

        $this->assertEquals($roadStart, $road2->getPrevObject());
        $this->assertEquals($road2, $roadStart->getNextObject());
        $this->assertEquals($roadGoal, $road4->getNextObject());
        $this->assertEquals($road4, $roadGoal->getPrevObject());
    }

    public function testMergeRoadToMapOneItem()
    {
        $mapManager = new MapManager();
        $map = new Map();
        $roadMap = new Map();

        $roadStart = (new Map\Objects\RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_EXIT)
            ->setLeftSide('red_bottle');
        $roadGoal = (new Map\Objects\RoadObject(['x' => 2, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ENTRY)
            ->setLeftSide('red_bottle');

        $road2 = (new Map\Objects\RoadObject(['x' => 1, 'y' => 0]))
            ->setLeftSide('red_bottle');

        $map->addObject($roadStart);
        $map->addObject($roadGoal);
        $roadMap->addObject($road2);

        $mapManager->mergeRoadToMap($map, $roadMap, ['x' => 0, 'y' => 0]);

        $this->assertEquals($roadStart, $road2->getPrevObject());
        $this->assertEquals($road2, $roadStart->getNextObject());
        $this->assertEquals($roadGoal, $road2->getNextObject());
        $this->assertEquals($road2, $roadGoal->getPrevObject());
    }
}
