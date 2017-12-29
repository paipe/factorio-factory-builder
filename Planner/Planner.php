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
use Map\Map;
use PathFinder\PathFinder;

abstract class Planner
{


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




}