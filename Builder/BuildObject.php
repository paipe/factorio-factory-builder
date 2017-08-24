<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:34
 */

namespace Builder;


use Drawer\ArrayDrawer;

class BuildObject
{

    /**
     * @var string
     */
    public $productName;

    /**
     * Массив входящих элементов
     * 
     * @var array
     */
    public $in;

    /**
     * Количество фабрик (1+)
     * 
     * @var int
     */
    public $factoryCount;
    
    public function __construct($name, $in, $count)
    {
        $this->productName  = $name;
        $this->in           = $in;
        $this->factoryCount = $count;
    }
    
    public function buildFabric()
    {
        $width = $this->factoryCount * 3;
        $height = (count($this->in) > 1) ? 8 : 7;
        
        $fabric = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (in_array($y, [0, 6, 7], true)) {
                    $fabric[$y][$x] = 'lr';
                } elseif (in_array($y, [2, 3, 4], true)) {
                    $fabric[$y][$x] = 'ff';
                } elseif ($y === 1 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = 'um';
                } elseif ($y === 5 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = 'um';
                } elseif ($height === 8 && $y === 5 && $x % 3 === 0) {
                    $fabric[$y][$x] = 'uk';
                } else {
                    $fabric[$y][$x] = '__';
                }
            }
        }
        
        return $fabric;
    }
    
    public function show()
    {
        $fabric = $this->buildFabric();
        $drawer = new ArrayDrawer();
        $drawer->draw($fabric);
    }
}