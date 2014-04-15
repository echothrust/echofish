<?php
$this->breadcrumbs=array(
	'Sysconfs'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Sysconf','url'=>array('admin')),
);
?>

<h1>Create Sysconf</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>