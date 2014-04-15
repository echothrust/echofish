<?php
$this->breadcrumbs=array(
	'Sysconfs',
);

$this->menu=array(
	array('label'=>'Create Sysconf','url'=>array('create')),
	array('label'=>'Manage Sysconf','url'=>array('admin')),
);
?>

<h1>Sysconfs</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
