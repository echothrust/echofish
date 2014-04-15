<?php
$this->breadcrumbs=array(
	'Facilities'=>array('admin'),
	$model->name,
);

$this->menu=array(
array('label'=>'Create Facility','url'=>array('create')),
array('label'=>'Update Facility','url'=>array('update','id'=>$model->num)),
array('label'=>'Delete Facility','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->num),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage Facility','url'=>array('admin')),
);
?>

<h1>View Facility #<?php echo $model->num; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'name',
		'description',
		'num',
),
)); ?>
