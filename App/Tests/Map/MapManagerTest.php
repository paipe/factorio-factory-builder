<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.18
 * Time: 17:59
 */

namespace App\Tests\Map;

use App\Core\Map\Map;
use App\Core\Map\MapManager;
use App\Core\Map\Objects\ChestObject;
use App\Core\Map\Objects\RoadObject;
use App\Exceptions\PlaceToAddOccupiedException;
use PHPUnit\Framework\TestCase;

class MapManagerTest extends TestCase
{

    public function testMergeMaps()
    {
        $map1 = new Map();
        $map2 = new Map();
        $source1 = new ChestObject(['x' => 0, 'y' => 0]);
        $source2 = new ChestObject(['x' => 0, 'y' => 0]);
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
        $source1 = new ChestObject(['x' => 0, 'y' => 0]);
        $source2 = new ChestObject(['x' => 0, 'y' => 0]);
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

        $roadStart = (new RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $roadGoal = (new RoadObject(['x' => 4, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_GOAL)
            ->setLeftSide('red_bottle');

        $road2 = (new RoadObject(['x' => 1, 'y' => 0]))
            ->setLeftSide('red_bottle');
        $road3 = (new RoadObject(['x' => 2, 'y' => 0]))
            ->setLeftSide('red_bottle');
        $road4 = (new RoadObject(['x' => 3, 'y' => 0]))
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

        $roadStart = (new RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $roadGoal = (new RoadObject(['x' => 2, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_GOAL)
            ->setLeftSide('red_bottle');

        $road2 = (new RoadObject(['x' => 1, 'y' => 0]))
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
