<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.02.18
 * Time: 18:34
 */

namespace App\Tests\Map;

use App\Core\Map\Map;
use App\Core\Map\Objects\ChestObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Map\RoadManager;
use PHPUnit\Framework\TestCase;

class RoadManagerTest extends TestCase
{

    public function testFindPath()
    {
        $map = new Map();
        $roadStart = (new RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $roadGoal = (new RoadObject(['x' => 5, 'y' => 0]))
            ->setPointType(RoadObject::T_ROAD_GOAL)
            ->setLeftSide('red_bottle');
        $map->addObject($roadStart);
        $map->addObject($roadGoal);
        $roadManager = new RoadManager();
        $road = $roadManager->findPath($map, $roadStart, $roadGoal);

        $this->assertInstanceOf(Map::class, $road);

        /**
         * @var RoadObject $road0
         * @var RoadObject $road1
         * @var RoadObject $road2
         * @var RoadObject $road3
         * @var RoadObject $road4
         * @var RoadObject $road5
         */
        $road0 = $road->getObjectByCoordinates(['x' => 0, 'y' => 0]);
        $road1 = $road->getObjectByCoordinates(['x' => 1, 'y' => 0]);
        $road2 = $road->getObjectByCoordinates(['x' => 2, 'y' => 0]);
        $road3 = $road->getObjectByCoordinates(['x' => 3, 'y' => 0]);
        $road4 = $road->getObjectByCoordinates(['x' => 4, 'y' => 0]);
        $road5 = $road->getObjectByCoordinates(['x' => 5, 'y' => 0]);

        $this->assertNull($road0);
        $this->assertNull($road5);
        $this->assertContainsOnlyInstancesOf(
            RoadObject::class,
            [$road1, $road2, $road3, $road4]
        );

        $this->assertEquals($road2, $road1->getPrevObject());
        $this->assertEquals($road3, $road2->getPrevObject());
        $this->assertEquals($road4, $road3->getPrevObject());

        $this->assertEquals($road1, $road2->getNextObject());
        $this->assertEquals($road2, $road3->getNextObject());
        $this->assertEquals($road3, $road4->getNextObject());

        $this->assertEquals('red_bottle', $road1->getLeftSide());
        $this->assertEquals('red_bottle', $road2->getLeftSide());
        $this->assertEquals('red_bottle', $road3->getLeftSide());
        $this->assertEquals('red_bottle', $road4->getLeftSide());

        $this->assertNull($road1->getRightSide());
        $this->assertNull($road2->getRightSide());
        $this->assertNull($road3->getRightSide());
        $this->assertNull($road4->getRightSide());
    }

    public function testBuildSeparator()
    {
        $map = new Map();
        $roadStart = (new RoadObject(['x' => 4, 'y' => 1]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $roadGoalFirst = (new RoadObject(['x' => 0, 'y' => 1]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $roadGoalSecond = (new RoadObject(['x' => 0, 'y' => 5]))
            ->setPointType(RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $map->addObject($roadStart);
        $map->addObject($roadGoalFirst);
        $map->addObject($roadGoalSecond);
        $map->addObject(new ChestObject(['x' => 3, 'y' => 2]));

        $roadManager = new RoadManager();
        $combinations = $roadManager->buildSeparator(
            $map, [$roadStart, $roadGoalFirst, $roadGoalSecond]
        );

        /** @var RoadObject[] $combination */
        foreach ($combinations as $combination) {
            $this->assertTrue(count($combination) === 2);
            $this->assertEquals(RoadObject::T_ROAD_START, $combination['start']->getPointType());
            $this->assertEquals(RoadObject::T_ROAD_GOAL, $combination['goal']->getPointType());
        }

    }

}
