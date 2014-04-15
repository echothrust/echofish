<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */

$this->breadcrumbs=array(
	'Abuser Triggers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage AbuserTrigger', 'url'=>array('admin')),
	array('label'=>'Export Abuser Triggers Backup','url'=>array('export')),
);
?>

<h1>Create AbuserTrigger</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>