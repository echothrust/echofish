<?php
$this->breadcrumbs=array(
	'Syslogs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Syslog','url'=>array('index')),
	array('label'=>'Manage Syslog','url'=>array('admin')),
);
?>

<h1>Create Syslog</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>