<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.01.18
 * Time: 18:09
 */

namespace App\Core\Map;

use App\Core\Map;
use App\Core\Utils\Utils;

/**
 * Класс-надстройка для операций с картами
 *
 * Class MapManager
 * @package App\Core\Map
 */
class MapManager
{

    /**
     * Накладываем вторую карту на первую в указанных координатах
     *
     * @param Map $firstMap
     * @param Map $secondMap
     * @param array $coordinates
     *
     * @return Map|null
     */
    public function mergeMaps(Map $firstMap, Map $secondMap, $coordinates): ?Map
    {
        foreach ($secondMap->iterateMapObjects() as $object) {
            /** @var ObjectProto $object $x */
            $x = $coordinates['x'] + $object->getX();
            $y = $coordinates['y'] + $object->getY();
            $object->setX($x)->setY($y);
            $firstMap->addObject($object);
        }

        return $firstMap;
    }

}