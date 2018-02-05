<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.02.18
 * Time: 17:19
 */

namespace App\Core\Map;

/**
 * Интерфейс всех путей (дорога, трубы)
 *
 * Interface Conductor
 * @package App\Core\Map
 */
interface Conductor
{
    public function getPrevObject();
    public function getNextObject();
    public function setPrevObject(Conductor $object);
    public function setNextObject(Conductor $object);
    public function clearPrevObject();
    public function clearNextObject();
}