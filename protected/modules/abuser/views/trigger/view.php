<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */

$this->breadcrumbs=array(
	'Abuser Triggers'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AbuserTrigger', 'url'=>array('index')),
	array('label'=>'Create AbuserTrigger', 'url'=>array('create')),
	array('label'=>'Update AbuserTrigger', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AbuserTrigger', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AbuserTrigger', 'url'=>array('admin')),
);
?>

<h1>View AbuserTrigger #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'facility',
		'severity',
		'program',
		'msg',
		'pattern',
		'grouping',
		'capture',
		'description',
		'occurrence',
		'priority',
	),
)); ?>
