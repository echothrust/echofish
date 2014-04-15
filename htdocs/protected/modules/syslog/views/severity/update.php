<?php
$this->breadcrumbs=array(
	'Severities'=>array('admin'),
	$model->name=>array('view','id'=>$model->num),
	'Update',
);

	$this->menu=array(
	array('label'=>'Create Severity','url'=>array('create')),
	array('label'=>'View Severity','url'=>array('view','id'=>$model->num)),
	array('label'=>'Manage Severity','url'=>array('admin')),
	);
	?>

	<h1>Update Severity <?php echo $model->num; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>