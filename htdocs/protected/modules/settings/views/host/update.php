<?php
$this->breadcrumbs=array(
	'Hosts'=>array('admin'),
	$model->ip=>array('view','id'=>$model->id),
	'Update',
);

	$this->menu=array(
	array('label'=>'Create Host','url'=>array('create')),
	array('label'=>'View Host','url'=>array('view','id'=>$model->id)),
	array('label'=>'Resolve Host','url'=>'#','linkOptions'=>array('submit'=>array('resolve','id'=>$model->id))),
	array('label'=>'Manage Host','url'=>array('admin')),
	);
	?>

	<h1>Update Host ID:<?php echo $model->id?>/<?php echo $model->ip; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>