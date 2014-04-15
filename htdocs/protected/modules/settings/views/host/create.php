<?php
$this->breadcrumbs=array(
	'Hosts'=>array('admin'),
	'Create',
);

$this->menu=array(
array('label'=>'Manage Host','url'=>array('admin')),
);
?>

<h1>Create Host</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>