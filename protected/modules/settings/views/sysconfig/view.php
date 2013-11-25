<?php
$this->breadcrumbs=array(
	'Sysconfs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Sysconf','url'=>array('index')),
	array('label'=>'Create Sysconf','url'=>array('create')),
	array('label'=>'Update Sysconf','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Sysconf','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Sysconf','url'=>array('admin')),
);
?>

<h1>View Sysconf #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'val',
	),
)); ?>
