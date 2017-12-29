<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.10.17
 * Time: 11:40
 */

namespace PathFinder;


class SimpleNode extends NodeProto
{

    public function __construct($coords)
    {
        $this->x = $coords[1];
        $this->y = $coords[0];
    }

    public function calculateF()
    {
        $this->f = $this->h + $this->g;
    }

    public function compareNodes(NodeProto $node)
    {
        $result = false;
        if ($node->x == $this->x && $node->y == $this->y) {
            $result = true;
        }

        return $result;
    }
}