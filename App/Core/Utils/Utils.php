<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 12:04
 */

declare(strict_types=1);

namespace App\Core\Utils;

/**
 * Общий класс для различных утилит
 *
 * Class Utils
 * @package App\Core\Utils
 */
class Utils
{
    /**
     * Берет на вход два числа и возвращает их в массиве координат
     *
     * @param int $x
     * @param int $y
     * @return array
     */
    public static function c($x, $y)
    {
        return ['x' => $x, 'y' => $y];
    }

    /**
     * Возвращает координаты, являющиеся сдвигом вверх, влево,
     * вниз и вправо относительно переданных координат.
     *
     * @param array $coordinates
     * @return array
     * @throws \Exception
     */
    public static function getPossibleCoordinatesShift(array $coordinates): array
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

    /**
     * Возвращает true если переданный продукт является ресурсом-источником
     * (простейший продукт, не сборный)
     *
     * @todo: костыль по сути
     *
     * @param $productName
     * @return bool
     */
    public static function isSource($productName)
    {
        $source = [
            'iron_plate',
            'copper_plate'
        ];
        return in_array($productName, $source);
    }
}