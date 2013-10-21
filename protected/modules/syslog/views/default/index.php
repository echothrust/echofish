<?php
/* @var $this DefaultController */

/*$this->breadcrumbs=array(
	ucfirst($this->module->id),
);*/
$this->pageTitle='Echofish Syslog';
?>

<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Syslog Dashboard',
)); ?>

<p>
Manage different aspects of syslog messages.
</p>

<?php $this->endWidget(); ?>