<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.02.18
 * Time: 17:27
 */

namespace App\Tests\Map\Objects;

use App\Core\Map\Objects\SplitterObject;
use PHPUnit\Framework\TestCase;

class SplitterObjectTest extends TestCase
{

    public function testGetFirstExitCoordinates()
    {
        $splitter = new SplitterObject(['x' => 1, 'y' => 1]);
        $this->assertEquals(['x' => 0, 'y' => 1], $splitter->getFirstExitCoordinates());
    }

    public function testGetSecondExitCoordinates()
    {
        $splitter = new SplitterObject(['x' => 1, 'y' => 1]);
        $this->assertEquals(['x' => 0, 'y' => 2], $splitter->getSecondExitCoordinates());
    }

    public function testGetEntryCoordinates()
    {
        $splitter = new SplitterObject(['x' => 1, 'y' => 1]);
        $this->assertEquals(['x' => 2, 'y' => 1], $splitter->getEntryCoordinates());
    }

}
