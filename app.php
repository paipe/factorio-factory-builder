<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

$parser = new \Parser\Parser();
$tree = $parser->buildTree('red_bottle');


//TODO: учесть тот факт, что фабрика за раз может выкинуть > 1 предмета (напр: медный провод)
$builder = new \Builder\ObjectMapBuilder();
$builder->setTree($tree)->setCount(1);
$drawer = new \Drawer\ObjectMapGdDrawer();
$pathFinder = new \PathFinder\ObjectMapPathFinder();
$planner = new \Planner\ObjectMapPlanner($pathFinder);
$map = $planner->plan($builder->build());
$drawer->setMap($map);
$drawer->draw();