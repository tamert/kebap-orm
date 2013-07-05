<?php

/**
 * @param $params
 * @param array $columns
 * @todo : databese and model generator
 *
 *
 * $new = Builder::create('test')
 *          ->increments('id')
 *          ->string('title')
 *          ->text('body')
 *          ->boolean('is_active')
 *          ->apply();
 *
 * $drop = Builder::drop('test');
 */

/**
 * Class Builder
 */
class Builder
{
    /**
     * @var
     */
    static $table;

    /**
     * @var
     */
    public $_labels = array();

    /**
     * @var string default engine is InnoDB
     */
    public $engine = 'InnoDB';

    /**
     * @var string default charset is not defined
     */
    public $charset;


    // key > labelControl > keys

    public function labelControl($labal){

    }

    # create : start
    /**
     * @param $table
     * @param null $settings
     * @return Builder
     */
    static function create($table, $settings = null)
    {
        self::$table = $table;

        return new Builder;
    }

    static function drop($table)
    {

    }

    public function increments($label)
    {

    }

    public function string($label,$length = null)
    {


    }

    public function float($label)
    {

    }

    public function integer($label,$length = null)
    {

    }

    public function decimal($label,$one,$two)
    {

    }

    public function boolean($label)
    {

    }

    public function calldate($label)
    {

    }

    public function calltime($label)
    {

    }

    public function bigText($label)
    {

    }


    public function engine($type)
    {

    }

    public function charset($type)
    {

    }




}