<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.10.17
 * Time: 11:38
 */

namespace PathFinder;


use Builder\BuildObject;

class SimplePathFinder extends PathFinder
{
    private $map;
    private $openSet = [];
    private $closedSet = [];

    public function findPath($map, $start, $goal)
    {
        $this->map = $map;
        $path = $this->run($start, $goal);

        return $path;
    }

    private function run($start, $goal)
    {
        $goalNode = new Node($goal);
        $startNode = new Node($start);
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
                return $this->reconstructPath($x);
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

        return false;
    }

    private function heuristicCostEstimate($start, $goal)
    {
        return abs($start->x - $goal->x) + abs($start->y - $goal->y);
    }

    private function reconstructPath($goalNode)
    {
        $result = [];
        $currentNode = $goalNode;
        while ($currentNode != NULL) {
            $result[] = [$currentNode->y, $currentNode->x];
            $currentNode = $currentNode->cameFrom;
        }

        return $result;
    }

    /**
     * @return array
     */
    private function findNodeWithLowestF()
    {
        $min = new Node([0, 0]);
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

    /**
     * @param $node
     * @return Node[]
     */
    private function getNodeNeighbors($node)
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
            if (!isset($this->map[$node->y + $modifier['y']][$node->x + $modifier['x']])) {
                continue;
            }
            if ($this->map[$node->y + $modifier['y']][$node->x + $modifier['x']] != BuildObject::M_SPACE) {
                continue;
            }

            $result[] = new Node([$node->y + $modifier['y'], $node->x + $modifier['x']]);
        }

        return $result;
    }
}