<?php
$this->breadcrumbs=array(
	'Archives',
);

$this->menu=array(
	array('label'=>'Create Archive','url'=>array('create')),
	array('label'=>'Manage Archive','url'=>array('admin')),
);
?>

<h1>Archives</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
