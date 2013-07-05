<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 26.04.2013
 * Time: 02:35
 * To change this template use File | Settings | File Templates.
 */

class SystemException extends Exception{

    public function __construct($m,$c = 0,$p = null)
    {
        echo '<h1><center>'.$m.'</center></h1>';
    }
}