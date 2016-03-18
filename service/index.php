<?php

$expires = 60*60*24*30;

header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');

header("Cache-Control: Private");
header("Content-Type: text/html");
header("Content-Type: text/javascript");
header("Content-Type: text/css");
header("Content-Type: image/gif");
header("Content-Type: image/jpeg");
header("Content-Type: image/png");

// change the following paths if necessary
$yii    =   dirname(__FILE__).'/../yii/framework/yii.php';
$config =   dirname(__FILE__).'/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',0);

require_once($yii);
Yii::createWebApplication($config)->run();