<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.02.18
 * Time: 17:40
 */

namespace App\Tests\Utils;

use App\Core\Map;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\PathFinder\PathFinder;
use PHPUnit\Framework\TestCase;

class PathFinderTest extends TestCase
{
    public function testSimpleRun()
    {
        $map = new Map();
        $roadProto = new RoadObject(['x' => 0, 'y' => 0]);
        $pathFinder = new PathFinder($map, $roadProto);
        $road = $pathFinder->run(['x' => 0, 'y' => 0], ['x' => 3, 'y' => 0]);

        /**
         * @var RoadObject $road1
         * @var RoadObject $road2
         * @var RoadObject $road3
         * @var RoadObject $road4
         */
        $road1 = $road->getObjectByCoordinates(['x' => 0, 'y' => 0]);
        $road2 = $road->getObjectByCoordinates(['x' => 1, 'y' => 0]);
        $road3 = $road->getObjectByCoordinates(['x' => 2, 'y' => 0]);
        $road4 = $road->getObjectByCoordinates(['x' => 3, 'y' => 0]);

        $this->assertInstanceOf(Map::class, $road);
        $this->assertContainsOnlyInstancesOf(
            RoadObject::class,
            [$road1, $road2, $road3, $road4]
        );

        $this->assertEquals($road2, $road1->getNextObject());
        $this->assertEquals($road3, $road2->getNextObject());
        $this->assertEquals($road4, $road3->getNextObject());

        $this->assertEquals($road1, $road2->getPrevObject());
        $this->assertEquals($road2, $road3->getPrevObject());
        $this->assertEquals($road3, $road4->getPrevObject());
    }

    public function testOneBlockRoadRun()
    {
        $map = new Map();
        $roadProto = new RoadObject(['x' => 0, 'y' => 0]);
        $pathFinder = new PathFinder($map, $roadProto);
        $road = $pathFinder->run(['x' => 0, 'y' => 0], ['x' => 2, 'y' => 0]);

        /**
         * @var RoadObject $road1
         * @var RoadObject $road2
         * @var RoadObject $road3
         */
        $road1 = $road->getObjectByCoordinates(['x' => 0, 'y' => 0]);
        $road2 = $road->getObjectByCoordinates(['x' => 1, 'y' => 0]);
        $road3 = $road->getObjectByCoordinates(['x' => 2, 'y' => 0]);

        $this->assertInstanceOf(Map::class, $road);
        $this->assertContainsOnlyInstancesOf(
            RoadObject::class,
            [$road1, $road2, $road3]
        );

        $this->assertEquals($road2, $road1->getNextObject());
        $this->assertEquals($road3, $road2->getNextObject());

        $this->assertEquals($road1, $road2->getPrevObject());
        $this->assertEquals($road2, $road3->getPrevObject());
    }

    public function testEmptyBlockRoadRun()
    {
        $map = new Map();
        $roadProto = new RoadObject(['x' => 0, 'y' => 0]);
        $pathFinder = new PathFinder($map, $roadProto);
        $road = $pathFinder->run(['x' => 0, 'y' => 0], ['x' => 1, 'y' => 0]);

        /**
         * @var RoadObject $road1
         * @var RoadObject $road2
         */
        $road1 = $road->getObjectByCoordinates(['x' => 0, 'y' => 0]);
        $road2 = $road->getObjectByCoordinates(['x' => 1, 'y' => 0]);

        $this->assertInstanceOf(Map::class, $road);
        $this->assertContainsOnlyInstancesOf(
            RoadObject::class,
            [$road1, $road2]
        );

        $this->assertEquals($road2, $road1->getNextObject());

        $this->assertEquals($road1, $road2->getPrevObject());
    }
}
