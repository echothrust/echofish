<?php
/* @var $this TriggerController */
/* @var $model AbuserTrigger */

$this->breadcrumbs=array(
	'Abuser Triggers'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Manage Abuser Trigger', 'url'=>array('admin')),
  array('label'=>'Create Abuser Trigger', 'url'=>array('create')),
	array('label'=>'Update Abuser Trigger', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Abuser Trigger', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Export Abuser Triggers Backup','url'=>array('export')),
);
?>

<h1>View AbuserTrigger #<?php echo $model->id; ?></h1>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
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
