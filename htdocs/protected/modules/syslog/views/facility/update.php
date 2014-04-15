<?php
$this->breadcrumbs=array(
	'Facilities'=>array('admin'),
	$model->name=>array('view','id'=>$model->num),
	'Update',
);

	$this->menu=array(
	array('label'=>'Create Facility','url'=>array('create')),
	array('label'=>'View Facility','url'=>array('view','id'=>$model->num)),
	array('label'=>'Manage Facility','url'=>array('admin')),
	);
	?>

	<h1>Update Facility <?php echo $model->num; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>