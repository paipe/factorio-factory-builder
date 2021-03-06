<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

$time = -microtime(true);


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
$resultMap = (new \App\Core\Planner())->plan($buildingSchemes);

/**
 * Отрисовываем полученную схему
 */
(new \App\Core\Drawer())->setMap($resultMap)->draw();


$time += microtime(true);