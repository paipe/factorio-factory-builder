<?php

$loader = require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 18:30
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    
    public function testConstruct()
    {
        \App\Core\Utils\Logger::initialize();

        $iron = new \App\Core\Parser\Item('iron_plate');
        $ironGear = new \App\Core\Parser\Composite('iron_gear_wheel', 0.5);
        $ironGear->addItems($iron, 2);
        
        $parser = new \App\Core\Parser();
        $parserResult = $parser->buildTree('iron_gear_wheel');
        
        $this->assertEquals($ironGear, $parserResult);
    }

}
