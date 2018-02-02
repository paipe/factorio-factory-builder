<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 21.11.2017
 * Time: 21:38
 */

namespace Exceptions;


class UnsupportedMethodException extends \Exception
{
    protected $message = 'Unsupported method.';
    protected $code    = '1';
}