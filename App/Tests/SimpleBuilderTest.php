<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:21
 */
class SimpleBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $iron = new \App\Component\Item('iron');
        $copper = new \App\Component\Item('copper');
        $ironGear = new \App\Component\Composite('ironGear', 0.5);
        $ironGear->addItems($iron, 2);
        $redBottle = new \App\Component\Composite('redBottle', 5);
        $redBottle->addItems($ironGear, 1);
        $redBottle->addItems($copper, 1);

        $result = [
            new \Builder\BuildObject('redBottle', ['ironGear', 'copper'], 10),
            new \Builder\BuildObject('ironGear', ['iron'], 1),
            new \Builder\BuildObject('iron', [], 0),
            new \Builder\BuildObject('copper', [], 0),
        ];

        $builder = new \Builder\SimpleBuilder();
        $builder->setTree($redBottle);
        $builder->setCount(1);
        $this->assertEquals($result, $builder->build());
    }

}
