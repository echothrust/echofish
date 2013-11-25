<?php
$this->breadcrumbs=array(
	'Sysconfs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Sysconf','url'=>array('index')),
	array('label'=>'Create Sysconf','url'=>array('create')),
	array('label'=>'View Sysconf','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Sysconf','url'=>array('admin')),
);
?>

<h1>Update Sysconf <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>