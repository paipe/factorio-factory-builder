<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__.'/vendor/autoload.php';

$iron = new \Tree\Item('iron');
$copper = new \Tree\Item('copper');
$ironGear = new \Tree\Composite('ironGear', 0.5);
$ironGear->addItems($iron, 2);
$redBottle = new \Tree\Composite('redBottle', 5);
$redBottle->addItems($ironGear, 1);
$redBottle->addItems($copper, 1);

print_r(array_count_values($redBottle->countItems(1)));
print_r($redBottle->countConstructTime(1));

$context = new \FactoryBlock\FactoryContext();
$context->data = $redBottle->countConstructTime(1);
$context->root = 'redBottle';
$context->count = 1;

$factory = new \FactoryBlock\Factory();
$factory->setContext($context);
var_dump($factory->build());