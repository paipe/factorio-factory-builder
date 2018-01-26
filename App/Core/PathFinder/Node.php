<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:42
 */

namespace App\Core\PathFinder;

class Node
{
    public $f;
    public $g;
    public $x;
    public $h;
    public $y;

    /**
     * @var Node
     */
    public $cameFrom;

    public function __construct($coordinates)
    {
        $this->x = $coordinates['x'];
        $this->y = $coordinates['y'];
    }

    public function calculateF()
    {
        $this->f = $this->h + $this->g;
    }

    public function compareNodes(Node $node)
    {
        $result = false;
        if ($node->x == $this->x && $node->y == $this->y) {
            $result = true;
        }

        return $result;
    }

}