<?php
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/booster');
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Echofish',
	'runtimePath'=>'/tmp',
  'theme'=>'bootstrap',
	'preload'=>array(
	    'bootstrap',
			'log'
	),
	// autoloading model and component classes
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
		/*'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1',
			'generatorPaths'=>array('bootstrap.gii',),
		),*/
	),

	// application components
	'components'=>array(
    'bootstrap'=>array(
            'class'=>'bootstrap.components.Bootstrap',
                        'responsiveCss' => true,
            ),
		'user'=>array(
			'class'=>'WebUser',
			'allowAutoLogin'=>true,
		),
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		'session' => array(
    	'class' => 'CDbHttpSession',
      'connectionID' => 'db',
      'sessionTableName'=>'sessions',
      'autoCreateSessionTable' => false,
    ),
		// uncomment the following to use a MySQL database
		'db'=>require(dirname(__FILE__).'/db.php'),
		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),
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
	  'defaultPageSize'=>50,
	  'echofish_version'=>'0.4',
		'adminEmail'=>'info@echothrust.com',
	),
);