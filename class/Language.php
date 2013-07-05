<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hakan
 * Date: 25.05.2013
 * Time: 17:21
 * To change this template use File | Settings | File Templates.
 */

class Language extends Component
{

    public $active = 'tr' ;

    public function init()
    {
        //IF session open set active
    }

    public function change($id)
    {
        $this->active = $id;
        return $this;
    }

    public function getid()
    {
        return $this->active;
    }

}