<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

$time = -microtime(true);
\App\Core\Utils\Logger::initialize();
\App\Core\Utils\Logger::info('Application start');


$parser = new \App\Core\Parser();
$tree = $parser->buildTree('red_bottle');


//TODO: учесть тот факт, что фабрика за раз может выкинуть > 1 предмета (напр: медный провод)
$builder = new \App\Core\Builder();
$builder->setTree($tree)->setCount(0.1);
$drawer = new \App\Core\Drawer();
$pathFinder = new \App\Core\PathFinder();
$planner = new \App\Core\Planner($pathFinder);
$map = $planner->plan($builder->build());
$drawer->setMap($map);
$drawer->draw();

$time += microtime(true);

\App\Core\Utils\Logger::info('Application finish', [
    'time' => round($time, 2),
    'memory' => memory_get_peak_usage(true) / 1024 / 1024
]);