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

    const M_SPACE = '__';
    const M_FABRIC = 'ff';
    const M_LEFT_ROAD = 'lr';
    const M_UP_MANIPULATOR = 'um';
    const M_UP_KLEWN9 = 'uk';

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
                    $fabric[$y][$x] = self::M_LEFT_ROAD;
                } elseif (in_array($y, [2, 3, 4], true)) {
                    $fabric[$y][$x] = self::M_FABRIC;
                } elseif ($y === 1 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = self::M_UP_MANIPULATOR;
                } elseif ($y === 5 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = self::M_UP_MANIPULATOR;
                } elseif ($height === 8 && $y === 5 && $x % 3 === 0) {
                    $fabric[$y][$x] = self::M_UP_KLEWN9;
                } else {
                    $fabric[$y][$x] = self::M_SPACE;
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