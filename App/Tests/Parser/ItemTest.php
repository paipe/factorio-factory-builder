<?php

namespace App\Tests\Parser;

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 15:00
 */
class ItemTest extends \PHPUnit\Framework\TestCase
{

    public function testCountItems()
    {
        $item = new \App\Core\Parser\Item('copper');
        $this->assertEquals(['copper', 'copper', 'copper'], $item->countItems(3));
    }

}
