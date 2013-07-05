<?php

/**
 * Class Connection
 */
class Connection
{
    /**
     * database connection
     * @var $db
     */
    public static $connection;

    /**
     * @var array or null
     */
    private static $config = array();

    /**
     * @var array or null
     */
    private static $type = array();

    /**
     * @param array $config
     * @return PDO
     * @throws SystemException
     */
    public static function init($config = array())
    {

        if (!empty($config)) {

            self::$type = (isset($config['type'])) ? $config['type'] : null;
            self::$config = (isset($config['conf'])) ? $config['conf'] : null;
        } else {

            throw new SystemException("Error!:database config not found ", "dbase");
        }

        $database_path = null;

        // db account info

        $hostname = self::$config['host'];
        $database = self::$config['name'];
        $username = self::$config['user'];
        $password = self::$config['pass'];

        $charset = (isset(self::$config['charset'])) ? self::$config['charset'] : 'utf8';

        $string = null;
        $pdo_options = array();

        switch (self::$type) {
            case 'mysql':
                $string = "mysql:host=$hostname;dbname=$database";
                $pdo_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset");
                break;
            case 'pgsql':
                $string = "pqsql:host=$hostname;dbname=$database";
                break;
            case 'sqlite':
                $string = "sqlite:$database_path";
                break;
            case 'oracle':
                $string = "OCI:";
                break;
            case 'odbc':
                $string = "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=$database;Uid=$username";
                break;
            default:
                throw new SystemException('Error!: Driver '.self::$type.' not recognized in DB class ', "dbase");
        }

        return self::connect($string, $username, $password, $pdo_options);

    }

    /**
     * @param $string
     * @param $username
     * @param $password
     * @param array $pdo_options
     * @return PDO
     * @throws SystemException
     */
    static function connect($string, $username, $password, $pdo_options = array())
    {
        try {
            self::$connection = new PDO($string, $username, $password, $pdo_options);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (PDOException $e) {

            throw new SystemException("Error!: " . $e->getMessage() . "  ", "dbase");
        }
        return self::$connection;

    }

    /**
     * disconnect
     */
    static function disconnect()
    {
        unset(self::$connection);
    }

}