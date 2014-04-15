<h1>Manage Overall Syslog Counters</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'syslogcounters-grid',
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
