<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17.01.18
 * Time: 18:02
 */

declare(strict_types=1);

namespace App\Core\Utils;


use Monolog\Handler\StreamHandler;

/**
 * Кастомная обертка над Monolog Logger
 *
 * Class Logger
 * @package App\Core\Utils
 */
class Logger
{

    /**
     * @var Logger
     */
    private static $instance;

    /**
     * @var \Monolog\Logger
     */
    private static $logger;

    private function __construct()
    {
        self::$logger = new \Monolog\Logger('simple_logger');
        self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/log.txt'));
    }

    public static function initialize():void
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
    }

    public static function info(string $msg, array $context = []): void
    {
        self::$logger->info($msg, $context);
    }

    public static function debug(string $msg, array $context = []): void
    {
        self::$logger->debug($msg, $context);
    }

    public static function notice(string $msg, array $context = []): void
    {
        self::$logger->notice($msg, $context);
    }

    public static function warning(string $msg, array $context = []): void
    {
        self::$logger->warning($msg, $context);
    }

    public static function error(string $msg, array $context = []): void
    {
        self::$logger->error($msg, $context);
    }

    public static function critical(string $msg, array $context = []): void
    {
        self::$logger->critical($msg, $context);
    }

    public static function alert(string $msg, array $context = []): void
    {
        self::$logger->alert($msg, $context);
    }

    public static function emergency(string $msg, array $context = []): void
    {
        self::$logger->emergency($msg, $context);
    }

}