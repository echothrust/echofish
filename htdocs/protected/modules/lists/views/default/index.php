<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Lists',
)); ?>


<p>
Manage your Echofish White/Black/Red lists.
</p>
<?php $this->endWidget(); ?>
