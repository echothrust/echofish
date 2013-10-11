<?php
$this->breadcrumbs=array(
	'Whitelists',
);

$this->menu=array(
	array('label'=>'Create Whitelist','url'=>array('create')),
	array('label'=>'Manage Whitelist','url'=>array('admin')),
);
?>

<h1>Whitelists</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
