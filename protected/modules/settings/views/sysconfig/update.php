<?php
$this->breadcrumbs=array(
	'Sysconfs'=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Manage Sysconf','url'=>array('admin')),
  array('label'=>'Create Sysconf','url'=>array('create')),
	array('label'=>'View Sysconf','url'=>array('view','id'=>$model->id)),
);
?>

<h1>Update Sysconf <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>