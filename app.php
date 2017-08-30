<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__.'/vendor/autoload.php';

$parser = new \Parser\Parser();
$tree = $parser->buildTree('redBottle');

//print_r($tree->countConstructTime(0.1));

$builder = new \Builder\SimpleBuilder();
$builder->setTree($tree);
$builder->setCount(0.01);
$drawer = new \Drawer\ArrayDrawer();
$planner = new \Planner\SimplePlanner($builder, $drawer);
$planner->plan();

