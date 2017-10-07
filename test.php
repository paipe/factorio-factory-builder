<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.10.17
 * Time: 19:04
 */

$loader = require_once __DIR__.'/vendor/autoload.php';

$map = [
    ['__', '__', 'xx', '__', '__', '__'],
    ['__', '__', 'xx', '__', 'xx', '__'],
    ['__', '__', '__', '__', 'xx', '__'],
    ['__', 'xx', 'xx', '__', 'xx', '__'],
    ['__', 'xx', '__', '__', 'xx', '__'],
    ['__', '__', '__', 'xx', '__', '__'],
    ['__', 'xx', 'xx', '__', '__', '__']
];

$pathFinder = new \PathFinder\SimplePathFinder();
$path = $pathFinder->findPath($map, [0, 0], [6, 5]);
var_dump($path);
foreach ($path as $dot) {
    $map[$dot[0]][$dot[1]] = 'ss';
}

$drawer = new \Drawer\ArrayDrawer();
$drawer->draw($map, []);