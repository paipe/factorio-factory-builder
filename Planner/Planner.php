<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 13:25
 */

namespace Planner;


use Builder\Builder;
use Builder\BuildObject;
use Drawer\Drawer;
use PathFinder\PathFinder;

abstract class Planner
{
    const DISTANCE = 10;

    const OUT = 'out';
    const IN  = 'in';

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
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $schema = [];

    /**
     * @var array
     */
    protected $roadSchema;
    protected $roadIndex = 0;

    /**
     * Planner constructor.
     *
     * @param \PathFinder\PathFinder $pathFinder
     */
    public function __construct(PathFinder $pathFinder)
    {
        $this->pathFinder = $pathFinder;
    }

    /**
     * @param BuildObject[] $buildObjects
     */
    abstract public function plan($buildObjects);

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @return array
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param int $objectY
     * @param int $objectX
     * @param $object
     */
    protected function putObjectOnTheMap($objectY, $objectX, $object)
    {
        if (!is_array($object)) {
            $object = [[$object]];
        }

        $ySize = count($object);
        $xSize = count($object[0]);
        $maxY = max($objectY + $ySize - 1, count($this->map) - 1);
        $maxX = max($objectX + $xSize - 1, isset($this->map[0]) ? count($this->map[0]) - 1 : 0);

        if (!isset($this->map[$maxY]) || !isset($this->map[0][$maxX])) {
            for ($y = 0; $y <= $maxY; $y++) {
                for ($x = 0; $x <= $maxX; $x++) {
                    if (!isset($this->map[$y][$x])) {
                        $this->map[$y][$x] = BuildObject::M_SPACE;
                    }
                }
            }
        }

        for ($y = $objectY; $y < $objectY + $ySize; $y++) {
            for ($x = $objectX; $x < $objectX + $xSize; $x++) {
                $this->map[$y][$x] = $object[$y - $objectY][$x - $objectX];
            }
        }

    }

    /**
     * @param int $y
     * @param int $x
     * @param array $data
     */
    protected function addItemToScheme($y, $x, $data)
    {
        $this->schema[$y . ':' . $x] = $data;
    }

    /**
     * @param int $y
     * @param int $x
     * @param string $name
     */
    protected function addItemToRoadScheme($y, $x, $name)
    {
        $this->roadSchema[$y . ':' . $x] = $name;
    }

    protected function buildRoads()
    {
        $in = [];
        $out = [];
        foreach ($this->roadSchema as $coords => $item) {
            $nameValue = explode('_', $item);
            $direction = array_shift($nameValue);
            $productName = implode('_', $nameValue);
            switch ($direction) {
                case Planner::IN:
                    $coords = explode(':', $coords);
                    $coords[1] = $coords[1] + 1;
                    $coords = implode(':', $coords);
                    $in[$coords] = $productName;
                    break;
                case Planner::OUT:
                    $coords = explode(':', $coords);
                    $coords[1] = $coords[1] - 1;
                    $coords = implode(':', $coords);
                    $out[$coords] = $productName;
                    break;
            }
        }
        foreach ($out as $outCoords => $outDot) {
            foreach ($in as $inCoords => $inDot) {
                if ($outDot == $inDot) {
                    $road = $this->pathFinder->findPath($this->map, explode(':', $outCoords), explode(':', $inCoords));
                    //рисуем сразу после того, как построили путь, чтобы новый путь шел в обход построенных дорог
                    foreach ($road as $key => $dot) {
                        switch ($key) {
                            case 0:
                                $this->addItemToScheme($dot[0], $dot[1] - 1, ['name' => BuildObject::M_PATH, 'index' => $this->roadIndex, 'start' => ['y' => $dot[0], 'x' => $dot[1]]]);
                                break;
                            case count($road) - 1:
                                $this->addItemToScheme($dot[0], $dot[1] + 1, ['name' => BuildObject::M_PATH, 'index' => $this->roadIndex]);
                                break;
                        }
                        $this->putObjectOnTheMap($dot[0], $dot[1], BuildObject::M_PATH);
                        $this->addItemToScheme($dot[0], $dot[1], ['name' => BuildObject::M_PATH, 'index' => $this->roadIndex]);
                    }
                    $this->roadIndex++;
                }
            }
        }

    }

    protected function expandMap()
    {
        $height = count($this->map);
        $width = count($this->map[0]);

        $expandedMap = $this->map;
        $expandedMap = array_merge($expandedMap, array_fill($height, $height * 2, [BuildObject::M_SPACE]));
        $expandedMap[0] = array_merge($expandedMap[0], array_fill($width, $width / 4, BuildObject::M_SPACE));

        $newHeight = count($expandedMap);
        $newWidth = count($expandedMap[0]);
        for ($i = 0; $i < $newHeight; $i++) {
            for ($j = 0; $j < $newWidth; $j++) {
                if (!isset($expandedMap[$i][$j])) {
                    $expandedMap[$i][$j] = BuildObject::M_SPACE;
                }
            }
        }

        return $expandedMap;
    }


}