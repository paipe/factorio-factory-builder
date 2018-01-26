<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 12:04
 */

namespace App\Core\Utils;


class Utils
{
    public static function getCoords($x, $y)
    {
        return ['x' => $x, 'y' => $y];
    }

    public static function getPossibleCoordinatesShift($coordinates)
    {
        if (!isset($coordinates['x']) || !isset($coordinates['y'])) {
            throw new \Exception('getPossibleCoordinatesShift не корректные входные данные!');
        }
        $possibleCoordinatesDelta = [
            ['x' => 0, 'y' => 1],
            ['x' => 0, 'y' => -1],
            ['x' => 1, 'y' => 0],
            ['x' => -1, 'y' => 0]
        ];
        $result = [];
        foreach ($possibleCoordinatesDelta as $item) {
            $result[] = [
                'x' => $coordinates['x'] + $item['x'],
                'y' => $coordinates['y'] + $item['y']
            ];
        }

        return $result;
    }

    public static function isSource($productName)
    {
        $source = [
            'iron_plate',
            'copper_plate'
        ];
        return in_array($productName, $source);
    }
}