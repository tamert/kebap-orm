<?php
/**
 * Class Relationships
 */
class Relationships {

    public function get($var){

    }

    public function call($foo){

    }

    /**
     * @param $model
     * @param null $foreign
     * @param null $comp
     * @return bool
     */
    static  function belongsTo($model, $foreign = null, $comp = null)
    {
        return $model.$foreign.$comp;
    }

    /**
     * @param $model
     * @param null $foreign
     * @param null $comp
     * @return bool
     */
    static function hasMany($model, $foreign = null, $comp = null)
    {
        return $model.$foreign.$comp;
    }

    /**
     * @param $model
     * @param null $foreign
     * @param null $comp
     * @return bool
     */
    static function hasOne($model, $foreign = null, $comp = null)
    {
        return $model.$foreign.$comp;
    }


}