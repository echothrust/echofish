<?php
$this->breadcrumbs=array(
	'Syslogs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Syslog','url'=>array('index')),
	array('label'=>'Create Syslog','url'=>array('create')),
	array('label'=>'View Syslog','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Syslog','url'=>array('admin')),
);
?>

<h1>Update Syslog <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>