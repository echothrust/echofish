<?php
$this->breadcrumbs=array(
	'Severities',
);

$this->menu=array(
array('label'=>'Create Severity','url'=>array('create')),
array('label'=>'Manage Severity','url'=>array('admin')),
);
?>

<h1>Severities</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
'dataProvider'=>$dataProvider,
'itemView'=>'_view',
)); ?>
