<?php

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
        $iron = new \Tree\Item('iron');
        $ironGear = new \Tree\Composite('ironGear', 0.5);
        $ironGear->addItems($iron, 2);
        
        $parser = new \Parser\Parser();
        $parserResult = $parser->buildTree('ironGear');
        
        $this->assertEquals($ironGear, $parserResult);
    }

}
