<?php
/* @var $this TriggerController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Abuser Triggers',
);

$this->menu=array(
	array('label'=>'Create AbuserTrigger', 'url'=>array('create')),
	array('label'=>'Manage AbuserTrigger', 'url'=>array('admin')),
	array('label'=>'Export Abuser Triggers Backup','url'=>array('export')),
);
?>

<h1>Abuser Triggers</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
