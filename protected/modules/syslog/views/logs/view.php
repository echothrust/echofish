<?php
$this->breadcrumbs=array(
	'Syslogs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Syslog','url'=>array('index')),
	array('label'=>'Create Syslog','url'=>array('create')),
	array('label'=>'Update Syslog','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Syslog','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Syslog','url'=>array('admin')),
);
?>

<h1>View Syslog #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'host',
		'facility',
		'priority',
		'level',
		'program',
		'pid',
		'tag',
		'msg',
		'received_ts',
		'created_at',
		'updated_at',
	),
)); ?>
