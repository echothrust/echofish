<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Echofish',
	'preload'=>array('log'),
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.syslog.models.*',
	  'ext.YiiMailer.YiiMailer',
	),
	'modules'=>array(
		'syslog',
		'lists',
		'abuser',
		'statistics',
		'settings',
	),    
	// application components
	'components'=>array(
		'db'=>require(dirname(__FILE__).'/db.php'),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
	'params'=>array(
	  'echofish_version'=>'0.4',
		'adminEmail'=>'support@echothrust.com',
	),    
);