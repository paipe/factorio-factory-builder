<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:34
 */

namespace App\Core;


use App\Core\PathFinder\Node;
use App\Core\Map\Objects\EePointRoadObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Utils;

class PathFinder
{

    protected $closedSet;
    protected $openSet;

    /**
     * @var Map
     */
    protected $map;

    protected $goal;

    public function findPath($map, $start, $goal): ?Map
    {
        $this->map = $map;
        $this->openSet = [];
        $this->closedSet = [];
        //todo костыль для пересечения в конечной точке
        $this->goal = $goal;
        $path = $this->run($start, $goal);

        return $path;
    }

    /**
     * @param EePointRoadObject $start
     * @param EePointRoadObject $goal
     * @return Map|null
     *
     */
    private function run($start, $goal): ?Map
    {
        $startNode = new Node($start->getCoordinates());
        $goalNode  = new Node($goal->getCoordinates());
        $startNode->g = 0;
        $startNode->h = $this->heuristicCostEstimate($startNode, $goalNode);
        $startNode->calculateF();

        $this->openSet[] = $startNode;

        while (!empty($this->openSet)) {
            /**
             * @var Node $x
             */
            list($x, $xKey) = $this->findNodeWithLowestF();

            if ($x->compareNodes($goalNode)) {
                return $this->reconstructPath($x, $start);
            }

            unset($this->openSet[$xKey]);
            $this->closedSet[] = $x;

            $nodeNeighbors = $this->getNodeNeighbors($x);
            foreach ($nodeNeighbors as $neighbor) {
                foreach ($this->closedSet as $item) {
                    if ($neighbor->compareNodes($item)) {
                        continue 2;
                    }
                }

                $tentativeGScore = $x->g + 1;
                $neighborInOpenSet = false;
                foreach ($this->openSet as $item) {
                    if ($neighbor->compareNodes($item)) {
                        $neighborInOpenSet = true;
                    }
                }

                if (!$neighborInOpenSet) {
                    $this->openSet[] = $neighbor;
                    $tentativeIsBetter = true;
                } elseif ($tentativeGScore < $neighbor->g) {
                    $tentativeIsBetter = true;
                } else {
                    $tentativeIsBetter = false;
                }

                if ($tentativeIsBetter) {
                    $neighbor->cameFrom = $x;
                    $neighbor->g = $tentativeGScore;
                    $neighbor->h = $this->heuristicCostEstimate($neighbor, $goalNode);
                    $neighbor->calculateF();
                }
            }
        }

        return null;
    }

    private function heuristicCostEstimate(Node $start, Node $goal): int
    {
        return abs($start->x - $goal->x) + abs($start->y - $goal->y);
    }

    private function findNodeWithLowestF(): array
    {
        $min = new Node(['x' => 0, 'y' => 0]);
        $min->f = PHP_INT_MAX;
        $minKey = null;
        foreach ($this->openSet as $key => $node) {
            if ($node->f < $min->f) {
                $min = $node;
                $minKey = $key;
            }
        }
        return [$min, $minKey];
    }

    private function reconstructPath(Node $goalNode, RoadObject $road): Map
    {
        $pathMap = new Map();
        $currentNode = $goalNode;
        while ($currentNode != NULL) {
            $pathMap->addRoadObject(
                (new RoadObject(Utils::getCoords($currentNode->x, $currentNode->y)))
                    ->setLeftSide($road->getLeftSide())
                    ->setRightSide($road->getRightSide())
            );
            $currentNode = $currentNode->cameFrom;
        }

        return $pathMap;
    }

    private function getNodeNeighbors(Node $node): array
    {
        $arr1 = [-1, 0, 1];
        $arr2 = $arr1;
        $modifiers = [];
        foreach ($arr1 as $var1) {
            foreach ($arr2 as $var2) {
                if (($var1 == $var2 && $var1 == 0) || (abs($var1) - abs($var2)) == 0) {
                    continue;
                }
                $modifiers[] = ['x' => $var1, 'y' => $var2];
            }
        }

        $result = [];
        foreach ($modifiers as $modifier) {
            $coordinates = [
                'x' => $node->x + $modifier['x'],
                'y' => $node->y + $modifier['y']
            ];
            $object = $this->map->getObjectByCoordinates($coordinates);
            if (
                is_null($object) ||
                $object === $this->goal
            ) {
                $result[] = new Node($coordinates);
            }
        }

        return $result;
    }

}
