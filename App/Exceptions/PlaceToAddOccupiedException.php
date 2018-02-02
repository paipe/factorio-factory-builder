<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.02.18
 * Time: 15:33
 */

namespace App\Exceptions;


class PlaceToAddOccupiedException extends \Exception
{
    protected $message = 'Попытка добавить объект в уже занятую клетку карты.';
    protected $code    = '2';
}