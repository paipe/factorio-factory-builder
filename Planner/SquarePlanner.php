<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 14:23
 */

namespace Planner;


use Builder\BuildObject;

class SquarePlanner extends Planner
{
    const HEIGHT = 8;
    const DISTANCE = 10;

    const OUT = 'out';
    const IN  = 'in';

    /**
     * @var array
     */
    protected $roadSchema;
    protected $roadIndex = 0;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $schema = [];

    /**
     * Заполняет массивы карты и схемы для рисовальщика
     *
     * @param BuildObject[] $buildObjects
     */
    public function plan($buildObjects)
    {
        $y = 0;
        $x = 0;
        $combinations = $this->sortObjects($buildObjects);
        $buildObjectsCount = count($buildObjects);
        for ($i = 0; $i < $buildObjectsCount; $i++) {
            $width = $combinations['sequence'][$i];
            foreach ($buildObjects as $key => &$buildObject) {
                if ($buildObject->factoryCount * 3 == $width || ($width = 1 && $buildObject->factoryCount == 0)) {
                    $object = $buildObject;
                    unset($buildObjects[$key]);
                    break;
                }
            }

            if (isset($object) && $object->factoryCount > 0) {
                $this->putObjectOnTheMap($y, $x, $object->buildFabric());
                for ($k = 0; $k < $object->factoryCount; $k++) {
                    $this->addItemToScheme($y + 3, $x + ($k * 3) + 1, ['name' => $object->productName]);
                }
                //outer
                $this->addItemToRoadScheme($y, $x, self::OUT . '_' . $object->productName);
                for ($j = 0; $j < count($object->in); $j++) {
                    $this->addItemToRoadScheme($y + $j + 6, $x + ($object->factoryCount * 3) - 1, self::IN . '_' . $object->in[$j]);
                }
            } else {
                $this->putObjectOnTheMap($y, $x, $object->buildSource());
                $this->addItemToScheme($y, $x, ['name' => $object->productName]);
                $this->addItemToRoadScheme($y + 2, $x, self::OUT . '_' . $object->productName);
            }

            if (isset($combinations['gaps'][$i]) && $combinations['gaps'][$i] == 1) {
                $x = 0;
                $y += self::HEIGHT + self::DISTANCE;
            } else {
                $x += $object->factoryCount * 3 + self::DISTANCE;
            }

        }

        $this->buildRoads();

    }

    /**
     * На данный момент толком не работает
     *
     * @param BuildObject[] $buildObjects
     *
     * @return array
     */
    private function sortObjects($buildObjects)
    {
        $objectsWidth = [];
        foreach ($buildObjects as $buildObject) {
            $objectsWidth[] = $buildObject->factoryCount > 0 ?
                3 * $buildObject->factoryCount : 1;
        }
        $objectsGaps = count($buildObjects) - 1;
        $gapsCombinations = [];
        for ($i = 0; $i < pow(2, $objectsGaps); $i++) {
            $gapsCombinations[] = str_split(str_pad(decbin($i), $objectsGaps, "0", STR_PAD_LEFT));
        }
        $bestCombination = ['rate' => PHP_INT_MAX, 'square' => PHP_INT_MAX];
        foreach ($this->permute($objectsWidth) as $objectsSequence) {
            foreach ($gapsCombinations as $gapsCombination) {
                $countGaps = array_count_values($gapsCombination);
                $y = self::HEIGHT * (1 + (isset($countGaps[1]) ? $countGaps[1] : 0));
                $x = array_reduce($objectsSequence, function($carry, $item) use ($gapsCombination) {
                    $carry['current'] += $item;
                    if ($carry['current'] > $carry['max']) {
                        $carry['max'] = $carry['current'];
                    }
                    if (isset($gapsCombination[$carry['index']]) && $gapsCombination[$carry['index']] == 1) {
                        $carry['current'] = 0;
                    }
                    $carry['index']++;

                    return $carry;
                }, ['max' => 0, 'current' => 0, 'index' => 0])['max'];
                $kRate   = 100;
                $kSquare = 1;
                if ($x * $y * $kSquare < $bestCombination['square'] && abs($x - $y) * $kRate < $bestCombination['rate']) {
                    $bestCombination = [
                        'sequence' => $objectsSequence,
                        'gaps'     => $gapsCombination,
                        'rate'     => abs($x - $y) * $kRate,
                        'square'   => $x * $y * $kSquare
                    ];
                }
            }
        }
        unset($bestCombination['rate']);
        unset($bestCombination['square']);
        return $bestCombination;

    }

    /**
     * @param $items
     * @param array $perms
     *
     * @return \Generator
     */
    private function permute($items, $perms = [])
    {
        if (empty($items)) {
            yield $perms;
        }  else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newItems = $items;
                $newPerms = $perms;
                list($foo) = array_splice($newItems, $i, 1);
                array_unshift($newPerms, $foo);
                yield from $this->permute($newItems, $newPerms);
            }
        }
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
                case self::IN:
                    $in[$coords] = $productName;
                    break;
                case self::OUT:
                    $out[$coords] = $productName;
                    break;
            }
        }
        foreach ($out as $outCoords => $outDot) {
            foreach ($in as $inCoords => $inDot) {
                if ($outDot == $inDot) {
                    $road = $this->pathFinder->findPath($this->map, explode(':', $outCoords), explode(':', $inCoords));
                    //разворачиваем, т.к. в исходном варианте массив идет от конца дороги к началу, что не очень удобно
                    $road = array_reverse($road);
                    //рисуем сразу после того, как построили путь, чтобы новый путь шел в обход построенных дорог
                    foreach ($road as $key => $dot) {
                        switch ($key) {
                            case 0:
                                $this->addItemToScheme(
                                    $dot[0], $dot[1],
                                    [
                                        'name' => BuildObject::M_PATH,
                                        'index' => $this->roadIndex,
                                        'start' => ['y' => $dot[0], 'x' => $dot[1]]
                                    ]
                                );
                                break;
//                            case count($road) - 1:
//                                break;
                            default:
                                $this->addItemToScheme(
                                    $dot[0], $dot[1],
                                    [
                                        'name' => BuildObject::M_PATH,
                                        'index' => $this->roadIndex
                                    ]
                                );
                                break;
                        }
                        $this->putObjectOnTheMap($dot[0], $dot[1], BuildObject::M_PATH);
                    }
                    $this->roadIndex++;
                }
            }
        }

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