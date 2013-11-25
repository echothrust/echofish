<?php
$this->breadcrumbs=array(
	'Sysconfs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Sysconf','url'=>array('index')),
	array('label'=>'Manage Sysconf','url'=>array('admin')),
);
?>

<h1>Create Sysconf</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>