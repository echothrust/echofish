<?php
/* @var $this IncidentController */
/* @var $model AbuserIncident */

$this->breadcrumbs=array(
	'Abuser Incidents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AbuserIncident', 'url'=>array('index')),
	array('label'=>'Manage AbuserIncident', 'url'=>array('admin')),
);
?>

<h1>Create AbuserIncident</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>