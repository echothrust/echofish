<?php
$this->breadcrumbs=array(
	'Severities'=>array('admin'),
	'Create',
);

$this->menu=array(
array('label'=>'Manage Severity','url'=>array('admin')),
);
?>

<h1>Create Severity</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>