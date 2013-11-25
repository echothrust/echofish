<?php
$this->breadcrumbs=array(
	'Archives'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Archive','url'=>array('index')),
	array('label'=>'Create Archive','url'=>array('create')),
	array('label'=>'Update Archive','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Archive','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Archive','url'=>array('admin')),
);
?>

<h1>View Archive #<?php echo $model->id; ?></h1>

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
