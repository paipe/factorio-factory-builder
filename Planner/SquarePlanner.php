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
    const HEIGHT   = 8;

    public function plan()
    {
        $buildObjects = $this->builder->build();
        $this->prepare($buildObjects);
        $this->drawer->draw($this->map, $this->schema);
    }

    /**
     * @param BuildObject[] $buildObjects
     */
    private function prepare($buildObjects)
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

            if ($object->factoryCount > 0) {
                $this->putObjectOnTheMap($y, $x, $object->buildFabric());
                for ($k = 0; $k < $object->factoryCount; $k++) {
                    $this->addItemToScheme($y + 3, $x + ($k * 3) + 1, $object->productName);
                }
                //outer
                $this->addItemToRoadScheme($y, $x, self::OUT . $object->productName);
                for ($j = 0; $j < count($object->in); $j++) {
                    $this->addItemToRoadScheme($y + $j + 6, $x + ($object->factoryCount * 3) - 1, self::IN . $object->in[$j]);
                }
            } else {
                $this->putObjectOnTheMap($y, $x, $object->buildSource());
                $this->addItemToScheme($y, $x, $object->productName);
                $this->addItemToRoadScheme($y + 2, $x, self::OUT . $object->productName);
            }

            if (isset($combinations['gaps'][$i]) && $combinations['gaps'][$i] == 1) {
                $x = 0;
                $y += self::HEIGHT + self::DISTANCE;
            } else {
                $x += $object->factoryCount * 3 + self::DISTANCE;
            }

        }

        $this->map = $this->expandMap();
        $this->buildRoads();

    }

    /**
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
//                if (abs($x - $y) < $bestCombination['rate'] || (abs($x - $y) == $bestCombination['rate'] && $x * $y < $bestCombination['square'])) {
//                if ($x * $y < $bestCombination['square'] || ($x * $y == $bestCombination['square'] && abs($x - $y) < $bestCombination['rate'])) {
//                if ($x * $y + abs($x - $y) < $bestCombination['square'] + $bestCombination['rate']) {
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

    private function buildRoads()
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
                $this->buildRoad(explode(',', $outCoords), explode(',', $inCoords));
            }
        }
    }

    /**
     * @param array $start
     * @param array $goal
     */
    private function buildRoad($start, $goal)
    {
        $map = $this->map;
        $openList = [];
        $closedList = [];



    }

    private function expandMap()
    {
        $height = count($this->map);
        $width  = count($this->map[0]);

        $expandedMap = $this->map;
        $expandedMap = array_merge($expandedMap, array_fill($height, $height * 2, [BuildObject::M_SPACE]));
        $expandedMap[0] = array_merge($expandedMap[0], array_fill($width, $width / 4, BuildObject::M_SPACE));

        return $expandedMap;
    }

}