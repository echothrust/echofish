<?php
$this->breadcrumbs=array(
	'Archives'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Archive','url'=>array('index')),
	array('label'=>'Manage Archive','url'=>array('admin')),
);
?>

<h1>Create Archive</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>