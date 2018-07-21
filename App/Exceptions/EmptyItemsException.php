<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 21.11.2017
 * Time: 21:38
 */

namespace App\Exceptions;


class EmptyItemsException extends \Exception
{
    protected $message = 'Empty result on parse items.yaml file.';
    protected $code    = '0';
}