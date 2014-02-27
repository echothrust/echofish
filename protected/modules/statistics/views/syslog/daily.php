<h1>Manage Daily Syslog Counters</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'syslogdailycounters-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'ctype',
		array(
			'name'=>'name',
			'value'=>'$data->converted_name',
		),
		'val',
		'ts',
	),
)); ?>

