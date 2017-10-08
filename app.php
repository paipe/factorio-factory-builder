<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__.'/vendor/autoload.php';

$parser = new \Parser\Parser();
$tree = $parser->buildTree('red_bottle');

$builder = new \Builder\SimpleBuilder();
$builder->setTree($tree);
$builder->setCount(10);
$drawer = new \Drawer\GdDrawer();
$pathFinder = new \PathFinder\SimplePathFinder();
$planner = new \Planner\SquarePlanner($pathFinder);
$planner->plan($builder->build());
$drawer->draw($planner->getMap(), $planner->getSchema());