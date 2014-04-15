<h1>Manage Overall Archive Counters</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'archivecounters-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'ctype',
		array(
			'name'=>'name',
			'value'=>'$data->converted_name',
		),
		'val',
	),
)); ?>
