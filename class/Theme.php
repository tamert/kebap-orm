<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hakan
 * Date: 23.05.2013
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 */

class Theme extends Component{

    public $name = "deneme";
    public $path = 'themes';


    public function init()
    {

    }


    public function getBaseUrl($http = false)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . Base::app()->BaseUrl . '/'. $this->path. '/' . $this->name;
    }

    public function getViewPath()
    {
        return $this->path . '/'.$this->name.'/views/';
    }
}