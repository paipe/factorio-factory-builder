<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17.01.18
 * Time: 18:02
 */

namespace App\Core\Utils;


use Monolog\Handler\StreamHandler;

class Logger
{

    private static $instance;

    private static $logger;

    private function __construct()
    {
        self::$logger = new \Monolog\Logger('simple_logger');
        self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/log.txt'));
    }

    public static function initialize()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
    }

    public static function info($msg, $context = [])
    {
        self::$logger->info($msg, $context);
    }

    public static function debug($msg, $context = [])
    {
        self::$logger->debug($msg, $context);
    }

    public static function notice($msg, $context = [])
    {
        self::$logger->notice($msg, $context);
    }

    public static function warning($msg, $context = [])
    {
        self::$logger->warning($msg, $context);
    }

    public static function error($msg, $context = [])
    {
        self::$logger->error($msg, $context);
    }

    public static function critical($msg, $context = [])
    {
        self::$logger->critical($msg, $context);
    }

    public static function alert($msg, $context = [])
    {
        self::$logger->alert($msg, $context);
    }

    public static function emergency($msg, $context = [])
    {
        self::$logger->emergency($msg, $context);
    }

}