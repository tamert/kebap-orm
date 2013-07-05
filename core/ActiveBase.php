<?php

require_once(ActiveBase::path() . 'Connection.php');
require_once(ActiveBase::path() . 'DB.php');

require_once(ActiveBase::path() . 'Render.php');

require_once(ActiveBase::path() . '/generate/Relationships.php');
require_once(ActiveBase::path() . '/generate/Join.php');

require_once(ActiveBase::path() . 'Generate.php');
require_once(ActiveBase::path() . 'Builder.php');


/**
 * Class ActiveBase
 */
class ActiveBase
{

    /**
     * @var
     */
    public static $path;

    /**
     * Default
     * @return bool
     */
    public function afterSave()
    {
        return true;
    }

    /**
     * Default
     * @return bool
     */
    public function beforeSave()
    {
        return true;
    }

    /**
     * Default
     * @return bool
     */
    public function afterDelete()
    {
        return true;
    }

    /**
     * Default
     * @return bool
     */
    public function beforeDelete()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasOne()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasMany()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function belongsTo()
    {
        return false;
    }

    /**
     * @return string
     */
    static function path()
    {
        if (self::$path === null)
            self::$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR;
        return self::$path;
    }

    /**
     * @param array $wheres
     * @param null $combinations
     * @return mixed
     */
    public static function findAll($wheres = array(), $combinations = null)
    {
        $calling = get_called_class();
        return $calling::comb($combinations)->many($wheres)->get();
    }

    /**
     * @param array $wheres
     * @param null $combinations
     * @return mixed
     */
    public static function find($wheres = array(), $combinations = null)
    {
        $calling = get_called_class();
        return $calling::comb($combinations)->many($wheres)->limit(1)->get();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $calling = get_called_class();
        $tableName = call_user_func(array(new $calling, "tableName"));
        Generate::init($tableName, $calling);
        return call_user_func_array(array(new Generate, $name), $arguments);
    }

    public function __call($name, $arguments)
    {

        if ($name === 'tableName')
            return get_class($this);

        if ($name === 'save') {
            $calling = get_called_class();
            $values = (array)$this;
            return static::$calling($values);
        }

        return false;
    }

    /**
     * Start Set Relations
     * array(model,foreign = null,comp = array())
     */
    public function relations()
    {
        $hasOne = $this->hasOne();
        $hasMany = $this->hasMany();
        $belongsTo = $this->belongsTo();
        $objects = new stdClass();

        if ($hasOne && !empty($hasOne)) {
            foreach ($hasOne as $id => $value) {

                $objects->$id = Relationships::hasOne(isset($value[0]) ? $value[0] : null, isset($value[1]) ? $value[1] : null, isset($value[2]) ? $value[2] : array());
            }
        }
        if ($hasMany && !empty($hasMany)) {
            foreach ($hasMany as $id => $value) {
                $objects->$id = Relationships::hasMany(isset($value[0]) ? $value[0] : null, isset($value[1]) ? $value[1] : null, isset($value[2]) ? $value[2] : array());
            }

        }
        if ($belongsTo && !empty($belongsTo)) {
            foreach ($belongsTo as $id => $value) {
                $objects->$id = Relationships::belongsTo(isset($value[0]) ? $value[0] : null, isset($value[1]) ? $value[1] : null, isset($value[2]) ? $value[2] : array());
            }
        }
        return $objects;
    }

}