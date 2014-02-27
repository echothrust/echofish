<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>
<?php $this->widget('bootstrap.widgets.TbNavbar',array(
    		'brand'=>'<img width="25px" src="' . Yii::app()->baseUrl.'/images/logo-139x98-whitebg.png' . '" />',
    		'brandUrl'=>array('site/index'),
				'collapse'=>false,
				'fluid'=>true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array('label'=>'Home', 'url'=>array('/site/index')),
								array('label'=>'Syslog', 'url'=>'#','active'=>$this->module && $this->module->id=='syslog', 'visible'=>!Yii::app()->user->isGuest, 'items'=>array(
		                array('label'=>'Logs', 'url'=>array('/syslog/logs/admin'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Archive', 'url'=>array('/syslog/archive'),'visible'=>!Yii::app()->user->isGuest),
                )),
								array('label'=>'Lists', 'url'=>'#','active'=>$this->module && $this->module->id=='lists','visible'=>!Yii::app()->user->isGuest, 'items'=>array(
		                array('label'=>'White Lists', 'url'=>array('/lists/white/admin'),'visible'=>!Yii::app()->user->isGuest),
                )),

								array('label'=>'Abuser', 'url'=>'/abuser/default/index','active'=>$this->module && $this->module->id=='abuser','visible'=>!Yii::app()->user->isGuest, 'items'=>array(
		                array('label'=>'Incidents', 'url'=>array('/abuser/incident/admin'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Triggers', 'url'=>array('/abuser/trigger/admin'),'visible'=>!Yii::app()->user->isGuest),
                )),


								array('label'=>'Statistics', 'url'=>'#','active'=>$this->module && $this->module->id=='statistics','visible'=>!Yii::app()->user->isGuest, 'items'=>array(
		                array('label'=>'Todays Graphs', 'url'=>array('/statistics/default/index'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Syslog Overall counters', 'url'=>array('/statistics/syslog/overall'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Syslog Daily counters', 'url'=>array('/statistics/syslog/daily'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Archive Overall counters', 'url'=>array('/statistics/archive/overall'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'Archive Daily counters', 'url'=>array('/statistics/archive/daily'),'visible'=>!Yii::app()->user->isGuest),
                )),
								array('label'=>'Settings', 'url'=>'#','active'=>$this->module && $this->module->id=='settings','visible'=>!Yii::app()->user->isGuest, 'items'=>array(
		                array('label'=>'Users', 'url'=>array('/settings/user/admin'),'visible'=>!Yii::app()->user->isGuest),
		                array('label'=>'System Configuration', 'url'=>array('/settings/sysconfig'),'visible'=>!Yii::app()->user->isGuest),
                )),
                array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
                array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                
                            ),
        ),
    ),
)); ?>
<div class="container-fluid" id="page">

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by Echothrust Solutions.<br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
