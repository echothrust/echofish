<?php
$this->breadcrumbs=array(
	'Whitelists'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Whitelist','url'=>array('admin')),
);
?>

<h1>Create Whitelist</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>