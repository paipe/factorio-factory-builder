<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

$parser = new \Parser\Parser();
$tree = $parser->buildTree('electronic_circuit');

$builder = new \Builder\SimpleBuilder();
$builder->setTree($tree);
$builder->setCount(1);
$drawer = new \Drawer\GdDrawer();
$pathFinder = new \PathFinder\SimplePathFinder();
$planner = new \Planner\SquarePlanner($pathFinder);
$planner->plan($builder->build());
$drawer->draw($planner->getMap(), $planner->getSchema());