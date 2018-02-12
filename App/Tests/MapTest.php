<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.02.18
 * Time: 15:24
 */

namespace App\Tests;

use App\Core\Map;
use App\Core\Utils\Utils;
use App\Exceptions\PlaceToAddOccupiedException;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{

    public function testAddObject()
    {
        $map = new Map();
        $source = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);

        $returnedSource = $map->addObject($source);

        $this->assertEquals($source, $returnedSource);
    }

    public function testAddObjectToFilledPoint()
    {
        $this->expectException(PlaceToAddOccupiedException::class);
        $map = new Map();
        $source1 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);
        $source2 = new Map\Objects\ChestObject(['x' => 0, 'y' => 0]);

        $map->addObject($source1);
        $map->addObject($source2);
    }

    public function testGetObjectByCoordinates()
    {
        $map = new Map();
        $coordinates = ['x' => 0, 'y' => 0];
        $source = new Map\Objects\ChestObject($coordinates);
        $map->addObject($source);

        $returnedObject = $map->getObjectByCoordinates($coordinates);
        $returnedNull = $map->getObjectByCoordinates(['x' => 1, 'y' => 1]);

        $this->assertEquals($source, $returnedObject);
        $this->assertNull($returnedNull);
    }

    public function testIsEmptyCoordinates()
    {
        $map = new Map();
        $coordinates = ['x' => 0, 'y' => 0];
        $source = new Map\Objects\ChestObject($coordinates);
        $map->addObject($source);

        $returnedTrue = $map->isEmptyCoordinates($coordinates);
        $returnedFalse = $map->isEmptyCoordinates(['x' => 1, 'y' => 1]);

        $this->assertTrue($returnedTrue);
        $this->assertFalse($returnedFalse);
    }

    public function testGetRoadPoints()
    {
        $map = new Map();
        $source = new Map\Objects\ChestObject(['x' => 0, 'y' => 1]);
        $road = new Map\Objects\RoadObject(['x' => 1, 'y' => 0]);
        $roadEntry = (new Map\Objects\RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ROAD_GOAL);
        $roadExit = (new Map\Objects\RoadObject(['x' => 2, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ROAD_START);

        $map->addObject($source);
        $map->addObject($road);
        $map->addObject($roadEntry);
        $map->addObject($roadExit);
        $entryPoints = $map->getEntryPoints();
        $exitPoints = $map->getExitPoints();

        $this->assertTrue(is_array($entryPoints));
        $this->assertEquals($roadEntry, $entryPoints[0]);
        $this->assertCount(1, $entryPoints);

        $this->assertTrue(is_array($exitPoints));
        $this->assertEquals($roadExit, $exitPoints[0]);
        $this->assertCount(1, $exitPoints);
    }

    public function testStartEndRoadCombinations()
    {
        $map = new Map();
        $source = new Map\Objects\ChestObject(['x' => 0, 'y' => 1]);
        $road = new Map\Objects\RoadObject(['x' => 1, 'y' => 0]);
        $roadGoal = (new Map\Objects\RoadObject(['x' => 0, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ROAD_GOAL)
            ->setLeftSide('red_bottle');
        $firstRoadStart = (new Map\Objects\RoadObject(['x' => 2, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');
        $secondRoadStart = (new Map\Objects\RoadObject(['x' => 5, 'y' => 0]))
            ->setPointType(Map\Objects\RoadObject::T_ROAD_START)
            ->setLeftSide('red_bottle');

        $map->addObject($source);
        $map->addObject($road);
        $map->addObject($firstRoadStart);
        $map->addObject($secondRoadStart);
        $map->addObject($roadGoal);
        $combinations = $map->getStartEndRoadCombinations();

        $this->assertTrue(is_array($combinations));
        $this->assertTrue(is_array($combinations[0]));
        $this->assertCount(1, $combinations);
        $this->assertCount(3, $combinations[0]);
        $this->assertContainsOnlyInstancesOf(
            Map\Objects\RoadObject::class,
            $combinations[0]
        );
    }

    public function testGetWidth()
    {
        $map = new Map();
        $randX = rand(0, 100);
        $map->addObject(new Map\Objects\ChestObject(['x' => $randX, 'y' => 0]));

        $this->assertEquals(1 +$randX, $map->getWidth());
    }

    public function testGetHeight()
    {
        $map = new Map();
        $randY = rand(0, 100);
        $map->addObject(new Map\Objects\ChestObject(['x' => 0, 'y' => $randY]));

        $this->assertEquals(1 +$randY, $map->getHeight());
    }

}
