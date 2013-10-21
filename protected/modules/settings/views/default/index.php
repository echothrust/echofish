<?php
/* @var $this DefaultController */

$this->menu=array(
	array('label'=>'Users','url'=>array('user/admin')),
);
?>
<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit',array(
    'heading'=>CHtml::encode(Yii::app()->name).' Settings',
)); ?>


<p>
Manage different aspects of your Echofish installation. Create, Update and Delete Users. 
</p>
<?php $this->endWidget(); ?>
