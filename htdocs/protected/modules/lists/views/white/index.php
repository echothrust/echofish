<?php
$this->breadcrumbs=array(
	'Whitelists',
);

$this->menu=array(
	array('label'=>'Create Whitelist','url'=>array('create')),
	array('label'=>'Manage Whitelist','url'=>array('admin')),
	array('label'=>'Export Whitelists Backup','url'=>array('export')),
	array('label'=>'Import Whitelists Backup','url'=>array('upload')),
);
?>

<h1>Whitelists</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
