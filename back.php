<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('error_log','error.log');

define('RootPath',dirname(__FILE__));
define('Log',true);

require_once "core/Application.php";
$config = require_once 'core/Config.php';

$config['end'] = 'back';

$app = new Application($config);
