<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 26.04.2013
 * Time: 02:10
 * To change this template use File | Settings | File Templates.
 */

class Component {

    public function init()
    {

    }
    public function __get($name)
    {
        $getter = "get".$name;

        if(method_exists($this,$getter))
            return $this->$getter();
    }

    public function __set($name,$value)
    {
        $setter = "set".$name;
        if(method_exists($this,$setter))
            return $this->$setter($value);
    }

    public function __isset($name)
    {
        $getter='get'.$name;
        if(method_exists($this,$getter))
            return $this->$getter()!==null;
    }

    public function __unset($name)
    {
        $setter='set'.$name;
        if(method_exists($this,$setter))
            $this->$setter(null);
    }
}