<?php
/**
 * This file contains local application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense
$paramsLocal = file_exists($configRoot.'/params-local.php') ? require($configRoot.'/params-local.php') : array ();

return CMap::mergeArray(array(
	// Some default parameters can be here (they may be overwritten in $frontendParamsLocal
	'db.username' => '',
	'db.password' => '',
	'db.name'     => '',
	'db.host'     => 'localhost',
	'log'=>array(
        'class'=>'CLogRouter',
        'routes'=>array(
            array(
                'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                'ipFilters'=>array('127.0.0.1','192.168.14.252'),
            ),
        ),
    ),
), $paramsLocal);
