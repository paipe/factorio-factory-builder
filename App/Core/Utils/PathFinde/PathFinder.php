<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:34
 */

declare(strict_types=1);

namespace App\Core\Utils\PathFinder;


use App\Core\Map;
use App\Core\Map\Objects\RoadObject;
use App\Core\Utils\Utils;

/**
 * Класс для поиска пути из точки А в точку Б
 */
class PathFinder
{

    /**
     * @var array
     */
    protected $closedSet;

    /**
     * @var array
     */
    protected $openSet;

    /**
     * @var Map
     */
    protected $map;

    /**
     * @var Map\Conductor
     */
    protected $proto;

    public function __construct(Map $map, Map\Conductor $proto)
    {
        $this->map = $map;
        $this->proto = $proto;
        $this->openSet = [];
        $this->closedSet = [];
    }

    /**
     * @param array $start -- coordinates
     * @param array $goal -- coordinates
     * @return Map|null
     */
    public function run(array $start, array $goal): ?Map
    {
        $startNode = new Node($start);
        $goalNode  = new Node($goal);
        $startNode->g = 0;
        $startNode->h = $this->heuristicCostEstimate($startNode, $goalNode);
        $startNode->calculateF();

        $this->openSet[] = $startNode;

        while (!empty($this->openSet)) {
            /**
             * @var Node $x
             */
            [$x, $xKey] = $this->findNodeWithLowestF();

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

    private function reconstructPath(Node $goalNode): Map
    {
        $pathMap = new Map();
        $prevObject = null;
        $currentNode = $goalNode;
        while ($currentNode !== null) {
            $roadObject = new RoadObject(Utils::c($currentNode->x, $currentNode->y));
            if ($prevObject !== null) {
                /** @var Map\Conductor $prevObject */
                $roadObject->setNextObject($prevObject);
                $prevObject->setPrevObject($roadObject);
            }
            $pathMap->addObject($roadObject);
            $prevObject = $roadObject;
            $currentNode = $currentNode->cameFrom;
        }

        return $pathMap;
    }

    private function getNodeNeighbors(Node $node): array
    {
        $result = [];
        $possibleShifts = Utils::getPossibleCoordinatesShift($node->getCoordinates());
        foreach ($possibleShifts as $coordinates) {
            $object = $this->map->getObjectByCoordinates($coordinates);
            if (is_null($object)) {
                $result[] = new Node($coordinates);
            }
        }

        return $result;
    }

}
