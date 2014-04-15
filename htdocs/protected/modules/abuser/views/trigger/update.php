<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */

$this->breadcrumbs=array(
	'Abuser Triggers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Export Abuser Triggers Backup','url'=>array('export')),
	array('label'=>'Create AbuserTrigger', 'url'=>array('create')),
	array('label'=>'View AbuserTrigger', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AbuserTrigger', 'url'=>array('admin')),
);
?>

<h1>Update AbuserTrigger <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>