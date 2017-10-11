<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 07.10.17
 * Time: 19:04
 */

//$loader = require_once __DIR__.'/vendor/autoload.php';
//
//$map = [
//    ['__', 'xx', '__', '__', '__', 'xx', '__', 'xx', '__', '__', '__', 'xx'],
//    ['__', '22', '__', 'xx', '__', '__', '__', '__', '__', 'xx', '__', '__'],
//    ['__', 'xx', '__', 'xx', 'xx', 'ee', 'xx', 'xx', 'xx', 'xx', 'xx', '__'],
//    ['__', 'xx', '__', 'xx', '__', '__', '__', 'xx', '__', 'xx', '__', '__'],
//    ['__', '11', '__', 'xx', '__', 'xx', 'xx', 'xx', '__', 'xx', '__', 'xx'],
//    ['__', '__', '__', 'xx', '__', 'xx', '__', '__', '__', '__', '__', '__'],
//    ['__', 'xx', 'xx', 'xx', '__', '__', '__', 'xx', 'xx', 'xx', '__', '__']
//];
//
//$pathFinder = new \PathFinder\SimplePathFinder();
//$path = $pathFinder->findPath($map, [0, 0], [3, 6]);
//var_dump($path);
//foreach ($path as $dot) {
//    $map[$dot[0]][$dot[1]] = 'LL';
//}
//
//$drawer = new \Drawer\ArrayDrawer();
//$drawer->draw($map, []);

$a = ['a_0', 'a_1', 'a_1', 'a_1'];
$i = 0;
while (!empty($road = array_keys($a, 'a_' . $i))) {
    echo count($road);
    $i++;
}