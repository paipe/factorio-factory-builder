<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 07.07.2017
 * Time: 23:12
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

$container = new \App\App\AppContainer();
$context = new \App\App\AppContext();
$processor = new \App\App\AppProcessor();

$context->fill([
    \App\App\AppContext::K_NAME => 'red_bottle',
    \App\App\AppContext::K_COUNT_PER_SEC => 1,
    \App\App\AppContext::K_YAML_FILE => 'resources/items.yaml'
]);

$processor->setContainer($container);
$processor->setContext($context);
