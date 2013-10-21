<?php
$this->breadcrumbs=array(
	'Syslogs',
);

$this->menu=array(
	array('label'=>'Create Syslog','url'=>array('create')),
	array('label'=>'Manage Syslog','url'=>array('admin')),
);
?>

<h1>Syslogs</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
