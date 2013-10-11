<?php
$this->breadcrumbs=array(
	'Whitelists'=>array('admin'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Create Whitelist','url'=>array('create')),
	array('label'=>'Update Whitelist','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Whitelist','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Whitelist','url'=>array('admin')),
);
?>

<h1>View Whitelist #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'host',
		'facility',
		'level',
		'program',
		'pattern',
	),
)); ?>
