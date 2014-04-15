<?php
$this->breadcrumbs=array(
	'Hosts'=>array('admin'),
	$model->ip,
);

$this->menu=array(
array('label'=>'Manage Host','url'=>array('admin')),
array('label'=>'Create Host','url'=>array('create')),
array('label'=>'Update Host','url'=>array('update','id'=>$model->ip)),
array('label'=>'Resolve Host','url'=>'#','linkOptions'=>array('submit'=>array('resolve','id'=>$model->ip))),
array('label'=>'Delete Host','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->ip),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1>View Host #<?php echo $model->ip; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'ipoctet',
		'fqdn',
		'short',
		'description',
),
)); ?>
