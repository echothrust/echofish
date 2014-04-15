<?php
$this->breadcrumbs=array(
	'Severities'=>array('admin'),
	$model->name,
);

$this->menu=array(
array('label'=>'Create Severity','url'=>array('create')),
array('label'=>'Update Severity','url'=>array('update','id'=>$model->num)),
array('label'=>'Delete Severity','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->num),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage Severity','url'=>array('admin')),
);
?>

<h1>View Severity #<?php echo $model->num; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'name',
		'description',
		'num',
),
)); ?>
