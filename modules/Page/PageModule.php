<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 28.04.2013
 * Time: 01:27
 * To change this template use File | Settings | File Templates.
 */

class PageModule extends Module {

    public $id = 'Page';
    public $DefaultController = 'Default';
    public $DefaultAction = 'Index';

    public function init(){

        return parent::init();
    }
}