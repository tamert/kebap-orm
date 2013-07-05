<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 26.04.2013
 * Time: 02:44
 * To change this template use File | Settings | File Templates.
 */

class HttpException extends Exception {

    public function __construct($code,$message)
    {
        $this->code = $code;
        $this->message = $message;

        echo '<h1>'.$message.'</h1>';
    }
}