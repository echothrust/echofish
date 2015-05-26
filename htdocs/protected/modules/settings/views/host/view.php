<?php
$this->breadcrumbs=array(
	'Hosts'=>array('admin'),
	$model->ipoctet,
);

$this->menu=array(
array('label'=>'Manage Host','url'=>array('admin')),
array('label'=>'Create Host','url'=>array('create')),
array('label'=>'Update Host','url'=>array('update','id'=>$model->id)),
array('label'=>'Resolve Host','url'=>'#','linkOptions'=>array('submit'=>array('resolve','id'=>$model->id))),
array('label'=>'Delete Host','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>

<h1>View Host ID#<?php echo $model->id; ?>/<?php echo $model->ipoctet; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'id',
		'ipoctet',
		'fqdn',
		'short',
		'description',
),
)); ?>
