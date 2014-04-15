<?php
$this->breadcrumbs=array(
	'Whitelists'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Whitelist','url'=>array('admin')),
	array('label'=>'Export Whitelists Backup','url'=>array('export')),
	array('label'=>'Import Whitelists Backup','url'=>array('upload')),
);
?>

<h1>Create Whitelist</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>