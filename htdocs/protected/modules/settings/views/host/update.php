<?php
$this->breadcrumbs=array(
	'Hosts'=>array('admin'),
	$model->ip=>array('view','id'=>$model->ip),
	'Update',
);

	$this->menu=array(
	array('label'=>'Create Host','url'=>array('create')),
	array('label'=>'View Host','url'=>array('view','id'=>$model->ip)),
	array('label'=>'Resolve Host','url'=>'#','linkOptions'=>array('submit'=>array('resolve','id'=>$model->ip))),
	array('label'=>'Manage Host','url'=>array('admin')),
	);
	?>

	<h1>Update Host <?php echo $model->ip; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>