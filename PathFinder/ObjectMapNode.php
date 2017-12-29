<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:42
 */

namespace PathFinder;


class ObjectMapNode extends NodeProto
{

    public function __construct($coordinates)
    {
        $this->x = $coordinates['x'];
        $this->y = $coordinates['y'];
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