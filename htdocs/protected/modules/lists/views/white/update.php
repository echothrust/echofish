<?php
$this->breadcrumbs=array(
	'Whitelists'=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Whitelist','url'=>array('create')),
	array('label'=>'Manage Whitelist','url'=>array('admin')),
	array('label'=>'Export Whitelists Backup','url'=>array('export')),
	array('label'=>'Import Whitelists Backup','url'=>array('upload')),
);
?>

<h1>Update Whitelist <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>