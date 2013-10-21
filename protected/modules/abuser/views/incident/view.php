<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
	'Abuser Incidents'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AbuserIncident', 'url'=>array('index')),
	array('label'=>'Create AbuserIncident', 'url'=>array('create')),
	array('label'=>'Update AbuserIncident', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AbuserIncident', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AbuserIncident', 'url'=>array('admin')),
);
?>

<h1>View AbuserIncident #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'ipstr',
		'trigger_id',
		'counter',
		'first_occurrence',
		'last_occurrence',
		'ts',
	),
)); ?>
<pre>
<?php foreach($model->evidence as $arch):?>
<?php echo CHtml::encode($arch->msg),"\n";?>
<?php endforeach;?>
</pre>

