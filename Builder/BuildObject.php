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
    const M_ROAD_LEFT = 'lr';
    const M_INSERTER_UP = 'um';
    const M_LONG_HANDED_INSERTER_UP = 'ul';
    const M_CHEST = 'ch';

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
                    $fabric[$y][$x] = self::M_ROAD_LEFT;
                } elseif ($y === 2 && $x % 3 === 0) {
                    $fabric[$y][$x] = self::M_FABRIC;
//                } elseif ($y === 2 && ($x - 1) % 3 === 0) {
//                    $fabric[$y][$x] = $this->productName;
                } elseif ($y === 1 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = self::M_INSERTER_UP;
                } elseif ($y === 5 && ($x - 1) % 3 === 0) {
                    $fabric[$y][$x] = self::M_INSERTER_UP;
                } elseif ($height === 8 && $y === 5 && $x % 3 === 0) {
                    $fabric[$y][$x] = self::M_LONG_HANDED_INSERTER_UP;
                } else {
                    $fabric[$y][$x] = self::M_SPACE;
                }
            }
        }
        
        return $fabric;
    }

    public function buildSource()
    {
        return [
            [self::M_CHEST],
            [self::M_INSERTER_UP],
            [self::M_ROAD_LEFT]
        ];
    }
    
}
