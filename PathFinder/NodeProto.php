<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:38
 */

namespace PathFinder;


abstract class NodeProto
{
    public $f;
    public $g;
    public $x;
    public $h;
    public $y;

    /**
     * @var SimpleNode
     */
    public $cameFrom;

    abstract public function calculateF();

    abstract public function compareNodes(NodeProto $node);
}