<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tahir
 * Date: 27.04.2013
 * Time: 02:09
 * To change this template use File | Settings | File Templates.
 */

interface Log {
    public function add($type,$message);
    public function getLogs();
}