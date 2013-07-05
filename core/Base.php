<?php
defined('BeginTime') or define('BeginTime',microtime(true));
defined('CorePath')  or define('CorePath',dirname(__FILE__));

/**
 * class Base
 *
 * Aplication (Application.php) nesnesine
 * doğal olarak uygulamanın genel ayarlarına vs.
 * global olarak erişmek için kullanılır.
 *
 * Örneğin Application::config için herhangi bir yerde
 * Base::app()->config ile ulaşılabilir
 */
class Base {
    public static $app;

    // Translate Array
    public static $tArray = array();

    public static function app()
    {
        return self::$app;
    }

    public static function setApp($app){
        self::$app = $app;
    }

    public static function log($name,$message,$file = null)
    {
        if(!Log)
            return false;

        $logger = self::app()->logger;

        $logger->add($name,$message);
    }

    /**
     * Translate Method
     */
    public function t($folder,$str)
    {
        if(empty(self::$tArray))
        {
            self::$tArray = require_once($folder.'/'.self::app()->language->id.'.php');
        }

        return self::$tArray[$str];
    }
}