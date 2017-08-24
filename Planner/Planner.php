<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 13.08.2017
 * Time: 13:25
 */

namespace Planner;


use Builder\Builder;
use Drawer\Drawer;

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

    public function __construct(Builder $builder, Drawer $drawer)
    {
        $this->builder = $builder;
        $this->drawer  = $drawer;
    }

    abstract public function plan();



}