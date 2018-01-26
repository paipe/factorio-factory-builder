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


/**
 * Строим дерево на основе yaml файла с описанием предметов
 */
$tree = (new \App\Core\Parser())->buildTree('red_bottle');

/**
 * Строим отдельные мини-схемы, содержащие планы заводов,
 * источников ресурсов и так далее.
 *
 * @todo: учесть тот факт, что фабрика за раз может
 * @todo: выкинуть > 1 предмета (напр: медный провод)
 */
$buildingSchemes = (new \App\Core\Builder())->setTree($tree)->setCount(0.1)->build();

/**
 * Из небольших схем собираем одну большую, одновременно
 * с этим строим дороги для соединений отдельных мини-схем
 */
$resultMap = (new \App\Core\Planner(new \App\Core\PathFinder()))->plan($buildingSchemes);

/**
 * Отрисовываем полученную схему
 */
(new \App\Core\Drawer())->setMap($resultMap)->draw();


$time += microtime(true);
\App\Core\Utils\Logger::info('Application finish', [
    'time' => round($time, 2),
    'memory' => memory_get_peak_usage(true) / 1024 / 1024
]);