<?php
$this->breadcrumbs=array(
	'Hosts',
);

$this->menu=array(
array('label'=>'Create Host','url'=>array('create')),
array('label'=>'Manage Host','url'=>array('admin')),
);
?>

<h1>Hosts</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
'dataProvider'=>$dataProvider,
'itemView'=>'_view',
)); ?>
