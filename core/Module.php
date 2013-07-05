<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 28.04.2013
 * Time: 01:27
 * To change this template use File | Settings | File Templates.
 */

class Module {

    public $controllerPath  = 'controllers';
    public $viewPath        = 'view';

    public $DefaultController;
    public $DefaultAction;
    public function init()
    {
        Base::log('Module',get_class($this). ' modul init');
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